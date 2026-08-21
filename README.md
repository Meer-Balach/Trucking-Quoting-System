# Trucking-Quoting-System

An AI-powered instant quote system for BN Dulay Trux Ltd, using Cheetah Transport-style zone-based rates.

## Features

- AI Chat Interface — Customers describe shipments in plain English
- Manual Form — Traditional form fallback
- Instant Rate Calculation — Based on zone matrix + weight tables
- Email Delivery — Sends quote to customer and BND admin
- Branded Design — Matches the BNDUlay Trux color on the website

## Tech Stack

- Frontend: HTML, CSS, JavaScript (vanilla)
- Backend: Node.js + Express
- AI: OpenAI GPT-4o mini
- Email: Nodemailer + Gmail SMTP

## Setup

1. Install dependencies:
   npm install

2. Configure .env file with:
   OPENAI_API_KEY=sk-your-key-here
   EMAIL_USER=your-gmail@gmail.com
   EMAIL_PASS=your-16-char-app-password

3. Get OpenAI Key from:
   https://platform.openai.com/api-keys

4. Get Gmail App Password from:
   https://myaccount.google.com/apppasswords
   (requires 2-step verification)

5. Start server:
   npm start


6. Open index.html in your browser (or use VS Code Live Server)

## File Structure

bnd-ai-quote/
- index.html       Main page structure
- styles.css       Branded styling (BND colors)
- app.js           Frontend logic (chat, form, quote display)
- rates.js         Rate tables + calculation logic
- server.js        Backend API (AI + Email)
- package.json     Dependencies
- .env             Secret API keys (never commit!)
- README.md        This file

## Rate Structure (Russel metal Based)

- Zones: A–Q (17 areas in Lower Mainland BC)
- Weight Brackets: 0–30,000 lbs+
- Rate Columns: 1–5 (based on zone-to-zone distance)
- Add-ons: Residential, Remote, Long Material, Dangerous Goods, Power Tailgate
- Fuel Surcharge: 32% (adjustable in rates.js)

## Testing

Try these sample chats:
- "Ship 400 lbs from Burnaby to Surrey"
- "I need to move 1200 lbs of dangerous goods from Richmond to Delta"
- "500 lbs Downtown Vancouver to White Rock, residential delivery"

## Deployment (Future)

When ready to go live on WordPress:
1. Deploy backend to hosting (Vercel, Railway, or same server as WP)
2. Add frontend as Elementor Custom HTML widget OR iframe on /quote-request-form/ page
3. Update API URL from localhost:3000 to production URL

## Contact

- Canada: (604) 588-8760
- USA: 1-800-640-2919
- Email: info@bndulaytrux.com

