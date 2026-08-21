// ============ IMPORTS ============
require('dotenv').config();
const express = require('express');
const cors = require('cors');
const OpenAI = require('openai');
const nodemailer = require('nodemailer');

// ============ SETUP ============
const app = express();
app.use(cors());
app.use(express.json());

// Initialize OpenAI (only if key is provided)
let openai = null;
if (process.env.OPENAI_API_KEY && process.env.OPENAI_API_KEY !== 'your-openai-key-here') {
  openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
  console.log('✅ OpenAI configured');
} else {
  console.log('⚠️  OpenAI not configured (AI chat will be disabled)');
}

// Initialize Email (Microsoft 365 / Work Outlook — bndulaytrux.com)
let transporter = null;
if (process.env.EMAIL_USER && process.env.EMAIL_PASS && 
    process.env.EMAIL_USER !== 'your-gmail@gmail.com' && 
    process.env.EMAIL_USER !== 'your-email@example.com') {
  
  transporter = nodemailer.createTransport({
    host: 'smtp.office365.com',
    port: 587,
    secure: false,
    auth: {
      user: process.env.EMAIL_USER,
      pass: process.env.EMAIL_PASS
    },
    tls: {
      ciphers: 'TLSv1.2'
    }
  });
  
  console.log('✅ Email service configured (Microsoft 365 / Outlook)');
} else {
  console.log('⚠️  Email not configured (email sending will be disabled)');
}

// ============ HEALTH CHECK ============
app.get('/', (req, res) => {
  res.json({ 
    status: 'BND Quote System is running!',
    ai: openai ? 'enabled' : 'disabled',
    email: transporter ? 'enabled' : 'disabled'
  });
});

// ============ AI PARSE QUOTE ENDPOINT ============
app.post('/api/parse-quote', async (req, res) => {
  const { message, history = [], currentData = {} } = req.body;
  
  if (!openai) {
    return res.json({
      reply: `AI chat is not configured yet.<br><br>Please use the <strong>Manual Form</strong> tab, or set up your OpenAI API key in the .env file.`,
      data: null
    });
  }
  
  const systemPrompt = `You are a friendly quote assistant for BN Dulay Trux, a trucking company in BC, Canada.
Your job is to extract shipping details from customer messages and ask for any missing info.

Available Zones (map city name to zone letter):
A=Downtown Vancouver
B=Burnaby
C=Richmond
D=Delta
E=New Westminster
F=North Vancouver
G=Coquitlam
H=Port Coquitlam
I=Pitt Meadows
J=Maple Ridge
K=Surrey
L=Langley
M=White Rock
N=Abbotsford
O=Chilliwack
P=Mission
Q=West Vancouver

Available Add-ons:
- residential (Residential/YVR/Downtown delivery)
- remote (Deep Cove, Horseshoe Bay, Tsawwassen area)
- long_material (Material 10ft or longer)
- dangerous_goods (Hazmat/DG/firearms)
- power_tailgate (Requires liftgate)

Currently collected data: ${JSON.stringify(currentData)}

RULES:
1. Extract these fields when mentioned: pickup_zone (single letter), delivery_zone (single letter), weight (number in lbs), addons (array), name, email, phone
2. Map city names → zone letters (e.g., "Burnaby" = "B", "Surrey" = "K")
3. Convert kg to lbs if needed (1 kg = 2.205 lbs). Round to whole number.
4. If info is missing, ask ONE friendly question at a time
5. Always respond in JSON: { "reply": "your message to customer", "data": { extracted_fields } }
6. Only include NEW fields in "data" that you extracted this turn (not old ones)
7. Be conversational, warm, and professional
8. If they have pickup_zone + delivery_zone + weight, briefly confirm and mention the quote will appear
9. Keep replies SHORT (2-3 sentences max)

EXAMPLE:
User: "Ship 500lbs from Burnaby to Surrey"
Response: {
  "reply": "Got it! Shipping 500 lbs from Burnaby to Surrey. Any special requirements like residential delivery or a liftgate? I'll calculate your quote now.",
  "data": { "pickup_zone": "B", "delivery_zone": "K", "weight": 500 }
}`;

  try {
    const completion = await openai.chat.completions.create({
      model: 'gpt-4o-mini',
      messages: [
        { role: 'system', content: systemPrompt },
        ...history,
        { role: 'user', content: message }
      ],
      response_format: { type: 'json_object' },
      temperature: 0.7
    });
    
    const result = JSON.parse(completion.choices[0].message.content);
    console.log('🤖 AI extracted:', result.data);
    res.json(result);
  } catch (err) {
    console.error('❌ AI Error:', err.message);
    res.status(500).json({ 
      reply: 'Sorry, I had trouble processing that. Please try again, or switch to the Manual Form tab.',
      data: null
    });
  }
});

// ============ SEND QUOTE EMAIL ENDPOINT ============
app.post('/api/send-quote', async (req, res) => {
  const { customerEmail, adminEmail, quoteText, customerData, quote, quoteNumber } = req.body;
  
  if (!transporter) {
    return res.status(500).json({ 
      success: false, 
      error: 'Email service not configured. Please set EMAIL_USER and EMAIL_PASS in .env file.' 
    });
  }
  
  if (!customerEmail) {
    return res.status(400).json({ 
      success: false, 
      error: 'Customer email is required.' 
    });
  }
  
  const emailHTML = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee;">
      <div style="background: linear-gradient(135deg, #1B5D8B 0%, #256628 100%); color: white; padding: 25px; text-align: center;">
        <h1 style="margin: 0; letter-spacing: 2px;">BN DULAY TRUX</h1>
        <p style="margin: 5px 0 0; opacity: 0.9;">Your Shipping Quote</p>
      </div>
      
      <div style="padding: 25px; background: #f9f9f9;">
        <p>Hi ${customerData.name || 'there'},</p>
        <p>Thank you for choosing BN Dulay Trux! Here's your estimated quote:</p>
        
        <pre style="background: white; padding: 20px; border-radius: 8px; overflow-x: auto; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.5; border-left: 4px solid #1B5D8B;">${quoteText}</pre>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px;">
          <h3 style="color: #1B5D8B; margin-top: 0;">Ready to book?</h3>
          <p>Reply to this email or contact us:</p>
          <ul style="line-height: 2;">
            <li>🇨🇦 Canada: <strong>(604) 588-8760</strong></li>
            <li>🇺🇸 USA/Mexico: <strong>1-800-640-2919</strong></li>
            <li>✉️ Email: <a href="mailto:dispatch@bndulaytrux.com">dispatch@bndulaytrux.com</a></li>
          </ul>
        </div>
        
        <p style="color: #666; font-size: 0.9em; margin-top: 20px;">
          <em>* This quote is valid for 24 hours from the time of issue and assumes standard shipping conditions. 
          Final rate will be confirmed upon booking.</em>
        </p>
      </div>
      
      <div style="background: #1B5D8B; color: white; padding: 15px; text-align: center; font-size: 0.85em;">
        <p style="margin: 0;">BN Dulay Trux Ltd. | We Deliver.</p>
        <p style="margin: 5px 0 0;">
          <a href="https://www.bndulaytrux.com" style="color: white;">www.bndulaytrux.com</a>
        </p>
      </div>
    </div>
  `;
  
  const adminHTML = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto;">
      <div style="background: #1B5D8B; color: white; padding: 20px; text-align: center;">
        <h2 style="margin: 0;">🚛 New Quote Request</h2>
        <p style="margin: 5px 0 0;">${quoteNumber || 'BND-' + Date.now()}</p>
      </div>
      <div style="padding: 20px; background: #f9f9f9;">
        <h3>Customer Info</h3>
        <ul>
          <li><strong>Name:</strong> ${customerData.name || 'N/A'}</li>
          <li><strong>Email:</strong> ${customerData.email || customerEmail}</li>
          <li><strong>Phone:</strong> ${customerData.phone || 'N/A'}</li>
          <li><strong>Company:</strong> ${customerData.company || 'N/A'}</li>
        </ul>
        <h3>Quote Details</h3>
        <pre style="background: white; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 12px;">${quoteText}</pre>
        <p><strong>Action Required:</strong> Follow up with this customer to confirm booking.</p>
      </div>
    </div>
  `;
  
  try {
    await transporter.sendMail({
      from: `"BN Dulay Trux" <${process.env.EMAIL_USER}>`,
      to: customerEmail,
      subject: `Your BND Shipping Quote - ${quoteNumber || ''}`,
      html: emailHTML
    });
    
    console.log('📧 Quote sent to customer:', customerEmail);
    
    if (adminEmail) {
      await transporter.sendMail({
        from: `"BND Quote System" <${process.env.EMAIL_USER}>`,
        to: adminEmail,
        subject: `🚛 New Quote Request - ${customerData.name || 'Customer'} - ${quoteNumber || ''}`,
        html: adminHTML
      });
      console.log('📧 Notification sent to admin:', adminEmail);
    }
    
    res.json({ success: true, message: 'Emails sent successfully' });
  } catch (err) {
    console.error('❌ Email Error:', err.message);
    res.status(500).json({ success: false, error: err.message });
  }
});

// ============ START SERVER ============
const PORT = 3000;
app.listen(PORT, () => {
  console.log('');
  console.log('═══════════════════════════════════════');
  console.log(`✅ BND Quote Server running`);
  console.log(`🌐 http://localhost:${PORT}`);
  console.log('═══════════════════════════════════════');
  console.log('');
  console.log('Open index.html in your browser to test!');
  console.log('Press Ctrl+C to stop the server.');
});
