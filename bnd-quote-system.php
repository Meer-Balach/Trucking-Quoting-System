<?php
/**
 * Plugin Name: BN Dulay Trux - Instant Quote System
 * Description: All-In-One Delivery Quote System reading BND Weight Matrices & Dedicated Equipment Rates (+20% Base Rate Adjustment). Shortcode: [bnd_quote_form]
 * Version: 3.5.0
 * Author: BND Tech
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// 1. SHORTCODE OUTPUT (HTML + STYLES + ENGINE)
add_shortcode( 'bnd_quote_form', 'bnd_render_quote_form' );
function bnd_render_quote_form() {
    $ajax_url = admin_url( 'admin-ajax.php' );
    ob_start();
    ?>

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS STYLES -->
    <style>
      #bnd-quote-app {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
        color: #333333 !important;
        max-width: 950px !important;
        margin: 20px auto !important;
        background: #ffffff !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        line-height: 1.5 !important;
        text-align: left !important;
      }

      #bnd-quote-app * {
        box-sizing: border-box !important;
      }

      #bnd-quote-app .hidden {
        display: none !important;
      }

      #bnd-quote-app .header {
        background: linear-gradient(135deg, #1B5D8B 0%, #256628 100%) !important;
        color: #ffffff !important;
        padding: 35px 20px !important;
        text-align: center !important;
      }

      #bnd-quote-app .header h1 {
        font-family: 'Barlow Condensed', sans-serif !important;
        font-size: 2.5rem !important;
        letter-spacing: 2px !important;
        margin: 0 0 5px 0 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
      }

      #bnd-quote-app .header .tagline {
        opacity: 0.95 !important;
        font-size: 1rem !important;
        margin: 0 !important;
        color: #ffffff !important;
      }

      #bnd-quote-app .quote-forms {
        padding: 35px 30px !important;
        background: #ffffff !important;
      }

      #bnd-quote-app .quote-form-tier h2 {
        color: #1B5D8B !important;
        font-family: 'Barlow Condensed', sans-serif !important;
        font-size: 2rem !important;
        margin: 0 0 5px 0 !important;
        font-weight: 700 !important;
      }

      #bnd-quote-app .form-hint {
        color: #666666 !important;
        margin-bottom: 25px !important;
        font-size: 0.95rem !important;
      }

      #bnd-quote-app .quote-form-tier h3 {
        color: #1B5D8B !important;
        margin: 25px 0 12px 0 !important;
        font-family: 'Barlow Condensed', sans-serif !important;
        font-size: 1.3rem !important;
        letter-spacing: 1px !important;
        border-bottom: 2px solid #f0f0f0 !important;
        padding-bottom: 5px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
      }

      #bnd-quote-app .form-row {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 15px !important;
        margin-bottom: 15px !important;
      }

      #bnd-quote-app .form-group {
        display: flex !important;
        flex-direction: column !important;
        margin-bottom: 15px !important;
      }

      #bnd-quote-app .form-group label {
        font-weight: 600 !important;
        margin-bottom: 6px !important;
        color: #444444 !important;
        font-size: 0.95rem !important;
      }

      #bnd-quote-app .form-group input,
      #bnd-quote-app .form-group select,
      #bnd-quote-app .form-group textarea {
        width: 100% !important;
        padding: 10px 12px !important;
        border: 2px solid #ddd !important;
        border-radius: 6px !important;
        font-family: inherit !important;
        font-size: 0.95rem !important;
        background: #ffffff !important;
        color: #333333 !important;
      }

      #bnd-quote-app .form-group input:focus,
      #bnd-quote-app .form-group select:focus,
      #bnd-quote-app .form-group textarea:focus {
        outline: none !important;
        border-color: #1B5D8B !important;
      }

      #bnd-quote-app .form-group textarea {
        resize: vertical !important;
        min-height: 80px !important;
      }

      #bnd-quote-app .hint {
        font-size: 0.8rem !important;
        color: #888888 !important;
        margin-top: 4px !important;
        font-style: italic !important;
      }

      #bnd-quote-app .oversized-inline {
        width: 100% !important;
        padding: 10px !important;
        border: 1px dashed #ffb300 !important;
        border-radius: 6px !important;
        background: #fff8e1 !important;
        font-size: 0.88rem !important;
        font-family: inherit !important;
        min-height: 55px !important;
        color: #5d4037 !important;
      }

      #bnd-quote-app .rate-notice {
        background: #fff8e1 !important;
        border-left: 4px solid #ffb300 !important;
        padding: 14px 16px !important;
        border-radius: 8px !important;
        margin: 20px 0 !important;
        font-size: 0.9rem !important;
        color: #6d4c00 !important;
        line-height: 1.5 !important;
      }

      #bnd-quote-app .btn-primary {
        background: #1B5D8B !important;
        color: #ffffff !important;
        border: none !important;
        padding: 14px 24px !important;
        border-radius: 6px !important;
        font-size: 1.05rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        font-family: inherit !important;
        width: 100% !important;
        text-align: center !important;
      }

      #bnd-quote-app .btn-primary:hover {
        background: #256628 !important;
      }

      #bnd-quote-app .btn-secondary {
        background: #dddddd !important;
        color: #333333 !important;
        border: none !important;
        padding: 12px 20px !important;
        border-radius: 6px !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        font-family: inherit !important;
        width: 100% !important;
        text-align: center !important;
      }

      #bnd-quote-app .btn-secondary:hover {
        background: #cccccc !important;
      }

      #bnd-quote-app .custom-quote-section {
        background: linear-gradient(135deg, rgba(240, 147, 251, 0.08), rgba(245, 87, 108, 0.08)) !important;
        border-top: 3px dashed #f5576c !important;
        padding: 30px 20px !important;
        text-align: center !important;
        margin-top: 30px !important;
        border-radius: 12px !important;
      }

      #bnd-quote-app .best-rates-badge {
        display: inline-block !important;
        background: #256628 !important;
        color: #ffffff !important;
        padding: 6px 15px !important;
        border-radius: 20px !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        margin-bottom: 12px !important;
      }

      #bnd-quote-app .custom-quote-section h3 {
        color: #333333 !important;
        font-size: 1.3rem !important;
        margin-bottom: 10px !important;
        border: none !important;
      }

      #bnd-quote-app .custom-quote-section p {
        color: #555555 !important;
        margin-bottom: 20px !important;
      }

      #bnd-quote-app .contact-buttons {
        display: flex !important;
        justify-content: center !important;
        gap: 15px !important;
        flex-wrap: wrap !important;
      }

      #bnd-quote-app .contact-btn {
        background: #ffffff !important;
        padding: 12px 22px !important;
        border-radius: 10px !important;
        border: 1px solid #e0e0e0 !important;
        text-decoration: none !important;
        color: #333333 !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05) !important;
      }

      #bnd-quote-app .contact-btn:hover {
        border-color: #1B5D8B !important;
        color: #1B5D8B !important;
      }

      #bnd-quote-app .quote-result {
        padding: 30px !important;
        background: #f9f9f9 !important;
        border-top: 4px solid #1B5D8B !important;
      }

      #bnd-quote-app .quote-result h2 {
        color: #1B5D8B !important;
        margin-bottom: 20px !important;
        font-family: 'Barlow Condensed', sans-serif !important;
        font-size: 1.8rem !important;
      }

      #bnd-quote-app #quote-details {
        background: #ffffff !important;
        padding: 20px !important;
        border-radius: 8px !important;
        margin-bottom: 20px !important;
        font-family: 'Courier New', monospace !important;
        white-space: pre-wrap !important;
        line-height: 1.6 !important;
        font-size: 0.85rem !important;
        border: 1px solid #eeeeee !important;
        overflow-x: auto !important;
        color: #222222 !important;
      }

      #bnd-quote-app .quote-actions {
        display: flex !important;
        gap: 12px !important;
      }

      @media (max-width: 600px) {
        #bnd-quote-app .form-row,
        #bnd-quote-app .contact-buttons,
        #bnd-quote-app .quote-actions {
          grid-template-columns: 1fr !important;
          flex-direction: column !important;
        }
      }
    </style>

    <!-- FORM HTML -->
    <div id="bnd-quote-app">
      <div class="container">
        
        <header class="header">
          <h1>BN DULAY TRUX</h1>
          <p class="tagline">Instant Delivery Quote System</p>
        </header>

        <div id="quote-forms" class="quote-forms">
          <div id="local-form" class="quote-form-tier">
            <h2>🚚 Delivery Quote</h2>
            <p class="form-hint">Lower Mainland BC & Island Routes — Automated Pricing</p>
            
            <form id="local-quote-form">
              <h3>Contact Information</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Email *</label>
                  <input type="email" name="email" required placeholder="you@example.com">
                </div>
                <div class="form-group">
                  <label>Phone *</label>
                  <input type="tel" name="phone" required placeholder="(604) 555-1234">
                </div>
              </div>
              
              <h3>Route</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Pickup City *</label>
                  <select name="pickup_city" required>
                    <option value="">-- Select Pickup City --</option>
                    <option value="Abbotsford">Abbotsford</option>
                    <option value="Aldergrove">Aldergrove</option>
                    <option value="Burnaby">Burnaby</option>
                    <option value="Chilliwack">Chilliwack</option>
                    <option value="Coquitlam">Coquitlam</option>
                    <option value="Delta">Delta</option>
                    <option value="Langley">Langley</option>
                    <option value="Maple Ridge">Maple Ridge</option>
                    <option value="Mission">Mission</option>
                    <option value="Nanaimo">Nanaimo (Island)</option>
                    <option value="New Westminster">New Westminster</option>
                    <option value="North Vancouver">North Vancouver</option>
                    <option value="Port Coquitlam">Port Coquitlam</option>
                    <option value="Port Moody">Port Moody</option>
                    <option value="Richmond">Richmond</option>
                    <option value="Rosedale">Rosedale</option>
                    <option value="Surrey">Surrey</option>
                    <option value="Vancouver">Vancouver</option>
                    <option value="Victoria">Victoria (Island)</option>
                    <option value="West Vancouver">West Vancouver</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Delivery City *</label>
                  <select name="delivery_city" required>
                    <option value="">-- Select Delivery City --</option>
                    <option value="Abbotsford">Abbotsford</option>
                    <option value="Aldergrove">Aldergrove</option>
                    <option value="Burnaby">Burnaby</option>
                    <option value="Chilliwack">Chilliwack</option>
                    <option value="Coquitlam">Coquitlam</option>
                    <option value="Delta">Delta</option>
                    <option value="Langley">Langley</option>
                    <option value="Maple Ridge">Maple Ridge</option>
                    <option value="Mission">Mission</option>
                    <option value="Nanaimo">Nanaimo (Island)</option>
                    <option value="New Westminster">New Westminster</option>
                    <option value="North Vancouver">North Vancouver</option>
                    <option value="Port Coquitlam">Port Coquitlam</option>
                    <option value="Port Moody">Port Moody</option>
                    <option value="Richmond">Richmond</option>
                    <option value="Rosedale">Rosedale</option>
                    <option value="Surrey">Surrey</option>
                    <option value="Vancouver">Vancouver</option>
                    <option value="Victoria">Victoria (Island)</option>
                    <option value="West Vancouver">West Vancouver</option>
                  </select>
                </div>
              </div>
              
              <h3>Shipment</h3>
              <div class="form-row">
                <div class="form-group">
                  <label>Weight (lbs) *</label>
                  <input type="number" name="weight" min="1" required placeholder="500">
                </div>
                <div class="form-group">
                  <label>Number of Pieces/Pallets (4x4 or less) *</label>
                  <input type="number" name="pieces" min="1" required placeholder="1">
                </div>
              </div>
              
              <div class="form-group oversized-group">
                <textarea name="oversized_info" rows="2" placeholder="If over 4x4, describe size & quantity (e.g., 2 pallets 6x4x5)" class="oversized-inline"></textarea>
                <small class="hint">⚠️ Anything larger than 4x4 requires a custom quote</small>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label>Truck Type *</label>
                  <select name="truck_type" required>
                    <option value="">-- Select Truck Type --</option>
                    <option value="5_ton">5 Ton Truck (max 15,000 lbs)</option>
                    <option value="10_ton">10 Ton Truck (max 28,000 lbs)</option>
                    <option value="tandem">Flatdeck Tandem (max 48,000 lbs)</option>
                    <option value="tridem">Flatdeck Tridem (max 60,000 lbs)</option>
                    <option value="super_b">Flatdeck Super B (max 85,000 lbs)</option>
                    <option value="chassis">Chassis (Custom Quote)</option>
                    <option value="curtain_van">Curtain Van (Custom Quote)</option>
                    <option value="dry_van">Dry Van (Custom Quote)</option>
                    <option value="rolltite">Rolltite / Conestoga (Custom Quote)</option>
                    <option value="not_sure">Not Sure - Let BND Recommend</option>
                  </select>
                  <small class="hint">Choose the truck type that best fits your load</small>
                </div>
              </div>
              
              <h3>Additional Information</h3>
              <div class="form-group">
                <label>Special Requests or Notes</label>
                <textarea name="additional" rows="3" placeholder="Tell us anything else that will help us serve you better — special handling, timing, access notes, etc."></textarea>
              </div>
              
              <div class="rate-notice">
                🤖 <strong>Automated Estimate Notice:</strong> These rates are automatically generated and may be inaccurate. Final pricing is subject to verification by our dispatch team. Rates are valid for <strong>24 hours</strong> from time of quote.
              </div>
              
              <button type="submit" class="btn-primary btn-large">Get Instant Quote</button>
            </form>
            
            <div class="custom-quote-section">
              <div class="best-rates-badge">💰 BEST RATES GUARANTEED</div>
              <h3>Need a Custom Quote or More Information?</h3>
              <p>For custom quotes, specialized loads, oversized pallets, or additional information, call or email us directly for the best rates!</p>
              
              <div class="contact-buttons">
                <a href="mailto:dispatch@bndulaytrux.com" class="contact-btn">
                  📧 dispatch@bndulaytrux.com
                </a>
                <a href="tel:+16045888760" class="contact-btn">
                  📞 604-588-8760
                </a>
              </div>
            </div>
          </div>
        </div>

        <div id="quote-result" class="quote-result hidden">
          <h2>📋 Your Quote</h2>
          <div id="quote-details"></div>
          <div class="quote-actions">
            <button id="email-quote-btn" class="btn-primary">📧 Email Me This Quote</button>
            <button id="new-quote-btn" class="btn-secondary">🔄 New Quote</button>
          </div>
        </div>
      </div>
    </div>

    <!-- CALCULATIONS & APP JAVASCRIPT -->
    <script>
      (function() {
        const FUEL_SURCHARGE = 0.32; // 32%
        
        const WEIGHT_LIMITS = {
          '5_ton': 15000,
          '10_ton': 28000,
          'tandem': 48000,
          'tridem': 60000,
          'super_b': 85000
        };

        // City to Zone Lookup
        const CITY_ZONES = {
          'Langley': 1, 'Aldergrove': 1,
          'Surrey': 2, 'Delta': 2,
          'Vancouver': 3, 'Richmond': 3,
          'Burnaby': 4, 'New Westminster': 4, 'Port Coquitlam': 4, 'Coquitlam': 4, 'Port Moody': 4,
          'Maple Ridge': 5, 'Mission': 5, 'Abbotsford': 5,
          'Chilliwack': 6, 'Rosedale': 6,
          'North Vancouver': 7, 'West Vancouver': 7,
          'Victoria': 'ISLAND', 'Nanaimo': 'ISLAND'
        };

        // RUSSEL METAL WEIGHT MATRIX (+20% Base Increase)
        // [minWeight, maxWeight, [Zone1, Zone2, Zone3, Zone4, Zone5, Zone6, Zone7]]
        const WEIGHT_MATRIX = [
          [0, 1499,       [144, 144, 144, 144, 144, 234, 204]],
          [1500, 2999,    [144, 144, 144, 144, 144, 258, 204]],
          [3000, 3999,    [144, 144, 144, 144, 168, 258, 204]],
          [4000, 5999,    [144, 144, 144, 144, 180, 258, 204]],
          [6000, 7999,    [144, 144, 144, 144, 180, 300, 204]],
          [8000, 8999,    [144, 144, 168, 144, 180, 300, 204]],
          [9000, 9999,    [144, 162, 168, 156, 192, 300, 204]],
          [10000, 11999,  [156, 168, 168, 168, 204, 300, 204]],
          [12000, 13999,  [156, 186, 168, 180, 204, 300, 204]],
          [14000, 24999,  [204, 204, 204, 204, 228, 354, 276]],
          [25000, 30000,  [240, 240, 240, 240, 276, 354, 300]]
        ];

        // DEDICATED EQUIPMENT FLAT RATES (+20% Base Increase)
        const DEDICATED_RATES = {
          '5_ton': {
            'Aldergrove': 144, 'Burnaby': 144, 'Coquitlam': 144, 'Delta': 144,
            'Langley': 144, 'Maple Ridge': 144, 'New Westminster': 144, 'Richmond': 144,
            'Surrey': 144, 'Abbotsford': 144, 'Vancouver': 144, 'Port Coquitlam': 144, 'Port Moody': 144,
            'Chilliwack': 180, 'Mission': 180, 'North Vancouver': 180, 'Rosedale': 180, 'West Vancouver': 180
          },
          '10_ton': {
            'Aldergrove': 168, 'Burnaby': 168, 'Coquitlam': 168, 'Delta': 168,
            'Langley': 168, 'Maple Ridge': 168, 'New Westminster': 168, 'Richmond': 168,
            'Surrey': 168, 'Abbotsford': 168, 'Vancouver': 168, 'Port Coquitlam': 168, 'Port Moody': 168,
            'North Vancouver': 192, 'West Vancouver': 192,
            'Chilliwack': 204, 'Mission': 204, 'Rosedale': 204
          },
          'tandem': {
            'Aldergrove': 402, 'Burnaby': 342, 'Coquitlam': 342, 'Delta': 342,
            'Langley': 342, 'Maple Ridge': 342, 'New Westminster': 342, 'Richmond': 342,
            'Surrey': 342, 'Abbotsford': 402, 'Mission': 402, 'Vancouver': 402, 'Port Coquitlam': 342, 'Port Moody': 342,
            'Chilliwack': 462, 'North Vancouver': 462, 'Rosedale': 462, 'West Vancouver': 462
          },
          'tridem': {
            'Aldergrove': 420, 'Burnaby': 360, 'Coquitlam': 360, 'Delta': 360,
            'Langley': 360, 'Maple Ridge': 360, 'New Westminster': 360, 'Richmond': 360,
            'Surrey': 360, 'Abbotsford': 420, 'Mission': 420, 'Vancouver': 420, 'Port Coquitlam': 360, 'Port Moody': 360,
            'Chilliwack': 480, 'North Vancouver': 480, 'Rosedale': 480, 'West Vancouver': 480
          },
          'super_b': {
            'Aldergrove': 540, 'Burnaby': 420, 'Coquitlam': 420, 'Delta': 420,
            'Langley': 420, 'Maple Ridge': 420, 'New Westminster': 420, 'Richmond': 420,
            'Surrey': 420, 'Abbotsford': 540, 'Vancouver': 540, 'Port Coquitlam': 420, 'Port Moody': 420,
            'North Vancouver': 540, 'West Vancouver': 540,
            'Chilliwack': 630, 'Mission': 630, 'Rosedale': 630
          }
        };

        const CUSTOM_QUOTE_TRUCKS = ['chassis', 'curtain_van', 'dry_van', 'rolltite', 'not_sure'];
        const TRUCK_TYPE_LABELS = {
          '5_ton': '5 Ton Truck', '10_ton': '10 Ton Truck', 'tandem': 'Flatdeck Tandem',
          'tridem': 'Flatdeck Tridem', 'super_b': 'Flatdeck Super B', 'chassis': 'Chassis',
          'curtain_van': 'Curtain Van', 'dry_van': 'Dry Van', 'rolltite': 'Rolltite / Conestoga',
          'not_sure': 'BND to recommend based on load'
        };

        function calculateLocalQuote(data) {
          const { pickup_city, delivery_city, weight, truck_type } = data;
          if (!pickup_city || !delivery_city) return { error: "Please select both cities." };
          if (!weight || weight <= 0) return { error: "Please enter a valid weight." };
          if (!truck_type) return { error: "Please select a truck type." };

          if (CUSTOM_QUOTE_TRUCKS.includes(truck_type)) {
            return { customQuote: true, tier: "Custom Quote Required", truck: truck_type, pickup: pickup_city, delivery: delivery_city, weight: weight };
          }

          const maxWeightLimit = WEIGHT_LIMITS[truck_type];
          if (maxWeightLimit && weight > maxWeightLimit) {
            return { customQuote: true, overweight: true, tier: "Custom Quote Required (Overweight)", truck: truck_type, pickup: pickup_city, delivery: delivery_city, weight: weight, weightLimit: maxWeightLimit };
          }

          let baseRate = 0;

          // ISLAND ROUTE EVALUATION (Victoria / Nanaimo) (+20%)
          if (pickup_city === 'Victoria' || pickup_city === 'Nanaimo' || delivery_city === 'Victoria' || delivery_city === 'Nanaimo') {
            if (truck_type === 'super_b') {
              baseRate = 2760; // ($2,300 + 20%)
            } else if (truck_type === 'tridem' || truck_type === 'tandem' || weight > 30000) {
              baseRate = 2400; // ($2,000 + 20%)
            } else {
              baseRate = 1620; // ($1,350 + 20%)
            }
          } else {
            // MAINLAND BC EVALUATION
            const pickupZone = CITY_ZONES[pickup_city];
            const deliveryZone = CITY_ZONES[delivery_city];

            // 1. Calculate Weight Matrix Rate (if <= 30,000 lbs)
            let matrixRate = 0;
            if (weight <= 30000 && pickupZone && deliveryZone) {
              const targetZone = Math.max(pickupZone, deliveryZone);
              const bracket = WEIGHT_MATRIX.find(row => weight >= row[0] && weight <= row[1]);
              if (bracket) {
                matrixRate = bracket[2][targetZone - 1];
              }
            }

            // 2. Calculate Dedicated Equipment Flat Rate
            let equipmentRate = 0;
            const truckTable = DEDICATED_RATES[truck_type];
            if (truckTable) {
              const pRate = truckTable[pickup_city] || 144;
              const dRate = truckTable[delivery_city] || 144;
              equipmentRate = Math.max(pRate, dRate);
            }

            // High-Point Evaluation
            baseRate = Math.max(matrixRate, equipmentRate);
          }

          if (!baseRate || baseRate <= 0) {
            return { error: "Selected city pair requires custom evaluation. Please call dispatch." };
          }

          const fuelSurcharge = baseRate * FUEL_SURCHARGE;
          const total = baseRate + fuelSurcharge;

          return {
            tier: "Local Delivery", pickup: pickup_city, delivery: delivery_city, weight: weight,
            truck: TRUCK_TYPE_LABELS[truck_type] || truck_type, truck_type: truck_type, baseRate: baseRate.toFixed(2),
            subtotal: baseRate.toFixed(2), fuelSurcharge: fuelSurcharge.toFixed(2),
            fuelSurchargePercent: (FUEL_SURCHARGE * 100).toFixed(0), total: total.toFixed(2)
          };
        }

        function initApp() {
          const localForm = document.getElementById('local-quote-form');
          if (localForm) localForm.addEventListener('submit', handleLocalSubmit);

          const emailBtn = document.getElementById('email-quote-btn');
          if (emailBtn) emailBtn.addEventListener('click', emailQuote);

          const newQuoteBtn = document.getElementById('new-quote-btn');
          if (newQuoteBtn) newQuoteBtn.addEventListener('click', newQuote);
        }

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initApp);
        } else {
          initApp();
        }

        function handleLocalSubmit(e) {
          e.preventDefault();
          const formData = new FormData(e.target);
          const data = {
            email: formData.get('email'), phone: formData.get('phone'),
            pickup_city: formData.get('pickup_city'), delivery_city: formData.get('delivery_city'),
            weight: parseFloat(formData.get('weight')), pieces: formData.get('pieces') || 1,
            oversized_info: formData.get('oversized_info') || '', truck_type: formData.get('truck_type'),
            truck_type_label: TRUCK_TYPE_LABELS[formData.get('truck_type')] || formData.get('truck_type'),
            additional: formData.get('additional') || ''
          };

          const quote = calculateLocalQuote(data);
          if (quote.error) { alert('⚠️ ' + quote.error); return; }
          if (quote.customQuote) { showCustomQuoteMessage(quote, data); return; }
          showQuote(quote, data);
        }

        function showQuote(quote, customerData) {
          const quoteNumber = 'BND-' + Date.now().toString().slice(-8);
          let details = '';
          details += `BN DULAY TRUX — DELIVERY QUOTE\n═══════════════════════════════════════\n\n`;
          details += `Date:         ${new Date().toLocaleDateString('en-CA')}\nQuote Number: ${quoteNumber}\n\n`;
          details += `CUSTOMER\n───────────────────────────────────────\nEmail:   ${customerData.email || 'N/A'}\nPhone:   ${customerData.phone || 'N/A'}\n\n`;
          details += `ROUTE\n───────────────────────────────────────\nPickup:   ${quote.pickup}\nDelivery: ${quote.delivery}\n\n`;
          details += `SHIPMENT\n───────────────────────────────────────\nWeight:      ${quote.weight} lbs\nPieces:      ${customerData.pieces} (4x4 or less)\nTruck Type:  ${customerData.truck_type_label}\n\n`;

          if (customerData.oversized_info && customerData.oversized_info.trim()) {
            details += `⚠️  OVERSIZED LOAD INFORMATION\n───────────────────────────────────────\n${customerData.oversized_info}\n\n`;
          }
          if (customerData.additional && customerData.additional.trim()) {
            details += `ADDITIONAL INFORMATION\n───────────────────────────────────────\n${customerData.additional}\n\n`;
          }

          details += `CHARGES\n───────────────────────────────────────\nBase Rate:                    $${quote.baseRate}\nFuel Surcharge (${quote.fuelSurchargePercent}%):         $${quote.fuelSurcharge}\n\n═══════════════════════════════════════\nTOTAL:                        $${quote.total} CAD\n═══════════════════════════════════════\n\n`;
          details += `⚠️  IMPORTANT NOTICE\n───────────────────────────────────────\nRates are automatically generated estimate calculations and subject to change.\nValid for 24 hours only. Quote applies to pallets/pieces 4x4 or less.\n\n`;

          document.getElementById('quote-details').textContent = details;
          document.getElementById('quote-result').classList.remove('hidden');
          document.getElementById('quote-result').scrollIntoView({ behavior: 'smooth', block: 'start' });
          window.currentQuote = { quote, customerData, details, quoteNumber };
        }

        function showCustomQuoteMessage(quote, customerData) {
          const quoteNumber = 'BND-' + Date.now().toString().slice(-8);
          let details = '';
          details += `BN DULAY TRUX — CUSTOM QUOTE REQUEST\n═══════════════════════════════════════\n\n`;
          details += `Date:            ${new Date().toLocaleDateString('en-CA')}\nRequest Number:  ${quoteNumber}\n\n`;
          details += `CUSTOMER\n───────────────────────────────────────\nEmail:   ${customerData.email || 'N/A'}\nPhone:   ${customerData.phone || 'N/A'}\n\n`;
          details += `SHIPMENT REQUEST\n───────────────────────────────────────\nPickup:      ${quote.pickup}\nDelivery:    ${quote.delivery}\nWeight:      ${quote.weight} lbs\nPieces:      ${customerData.pieces}\nTruck Type:  ${customerData.truck_type_label}\n\n`;

          if (quote.overweight) {
            details += `⚠️  WEIGHT EXCEEDS STANDARD LIMIT\n───────────────────────────────────────\nYour weight:     ${quote.weight} lbs\nTruck limit:     ${quote.weightLimit} lbs\nOverage:         ${quote.weight - quote.weightLimit} lbs\n\n`;
          }

          details += `═══════════════════════════════════════\n📞  CUSTOM DISPATCH QUOTE REQUIRED\n═══════════════════════════════════════\n\nPlease contact us directly:\n📞 CALL:    604-588-8760\n📧 EMAIL:   dispatch@bndulaytrux.com\n───────────────────────────────────────\n\n`;

          document.getElementById('quote-details').textContent = details;
          document.getElementById('quote-result').classList.remove('hidden');
          document.getElementById('quote-result').scrollIntoView({ behavior: 'smooth', block: 'start' });
          window.currentQuote = { quote, customerData, details, quoteNumber };
        }

        async function emailQuote() {
          if (!window.currentQuote) return;
          let emailToUse = window.currentQuote.customerData?.email || prompt('Enter your email:');
          if (!emailToUse) return;

          const btn = document.getElementById('email-quote-btn');
          const originalText = btn.textContent;
          btn.textContent = '⏳ Submitting...';
          btn.disabled = true;

          const payload = new URLSearchParams();
          payload.append('action', 'bnd_send_quote_email');
          payload.append('customerEmail', emailToUse);
          payload.append('quoteText', window.currentQuote.details);
          payload.append('quoteNumber', window.currentQuote.quoteNumber);
          payload.append('customerData', JSON.stringify(window.currentQuote.customerData));

          try {
            const res = await fetch('<?php echo esc_url($ajax_url); ?>', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: payload.toString()
            });
            const result = await res.json();
            if (result.success) {
              alert('✅ Quote submitted to ' + emailToUse + ' and BND Dispatch!');
              btn.textContent = '✅ Delivered!';
            } else {
              alert('❌ Delivery failed: ' + (result.data || 'Unknown error'));
              btn.textContent = originalText;
              btn.disabled = false;
            }
          } catch (err) {
            alert('❌ Connection error. Please call 604-588-8760.');
            btn.textContent = originalText;
            btn.disabled = false;
          }
        }

        function newQuote() {
          document.getElementById('quote-result').classList.add('hidden');
          document.querySelectorAll('#local-quote-form').forEach(f => f.reset());
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      })();
    </script>
    <?php
    return ob_get_clean();
}

// 2. WORDPRESS EMAIL BACKEND PROCESSOR
add_action( 'wp_ajax_bnd_send_quote_email', 'bnd_handle_single_file_email' );
add_action( 'wp_ajax_nopriv_bnd_send_quote_email', 'bnd_handle_single_file_email' );

function bnd_handle_single_file_email() {
    $customer_email = isset($_POST['customerEmail']) ? sanitize_email($_POST['customerEmail']) : '';
    $quote_text     = isset($_POST['quoteText']) ? wp_strip_all_tags($_POST['quoteText']) : '';
    $quote_number   = isset($_POST['quoteNumber']) ? sanitize_text_field($_POST['quoteNumber']) : 'BND-Estimate';
    $raw_customer   = isset($_POST['customerData']) ? stripslashes($_POST['customerData']) : '';
    
    $customer_data = json_decode($raw_customer, true);
    if (!$customer_data) { $customer_data = array(); }

    if ( empty($customer_email) || !is_email($customer_email) ) {
        wp_send_json_error('Invalid email address.');
    }

    $admin_email = 'dispatch@bndulaytrux.com';
    $subject = "Your BN Dulay Trux Estimate - " . $quote_number;
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $customer_html = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eeeeee;">
        <div style="background: linear-gradient(135deg, #1B5D8B 0%, #256628 100%); color: white; padding: 25px; text-align: center;">
            <h1 style="margin: 0; letter-spacing: 2px;">BN DULAY TRUX</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">Your Shipping Quote Estimate</p>
        </div>
        <div style="padding: 25px; background: #f9f9f9;">
            <p>Hello,</p>
            <p>Thank you for requesting an estimate with BN Dulay Trux! Below are the details of your quote:</p>
            <pre style="background: white; padding: 20px; border-radius: 8px; font-family: \'Courier New\', monospace; font-size: 13px; line-height: 1.5; border-left: 4px solid #1B5D8B; white-space: pre-wrap; overflow-x: auto;">' . esc_html($quote_text) . '</pre>
            <div style="background: white; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #eaeaea;">
                <h3 style="color: #1B5D8B; margin-top: 0;">Ready to book?</h3>
                <p>Reply to this email or contact us directly:</p>
                <ul style="line-height: 2; margin: 0; padding-left: 20px;">
                    <li>🇨🇦 Canada: <strong>(604) 588-8760</strong></li>
                    <li>✉️ Dispatch Email: <a href="mailto:dispatch@bndulaytrux.com">dispatch@bndulaytrux.com</a></li>
                </ul>
            </div>
            <p style="color: #777777; font-size: 0.85em; margin-top: 20px;">
                <em>* This automated estimate is valid for 24 hours. Final pricing is subject to confirmation by BND dispatch.</em>
            </p>
        </div>
        <div style="background: #1B5D8B; color: white; padding: 15px; text-align: center; font-size: 0.85em;">
            <p style="margin: 0;">BN Dulay Trux Ltd. | We Deliver.</p>
        </div>
    </div>';

    $admin_subject = "🚛 New Web Quote - " . (isset($customer_data['phone']) ? $customer_data['phone'] : $quote_number);
    $admin_html = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #dddddd;">
        <div style="background: #1b5d8b; color: white; padding: 20px; text-align: center;">
            <h2 style="margin: 0;">New Web Quote Request Received</h2>
            <p style="margin: 5px 0 0;">Estimate Reference: ' . esc_html($quote_number) . '</p>
        </div>
        <div style="padding: 20px; background: #fdfdfd;">
            <h3>Customer Contact Info</h3>
            <ul>
                <li><strong>Customer Email:</strong> ' . esc_html($customer_email) . '</li>
                <li><strong>Phone Contact:</strong> ' . esc_html(isset($customer_data['phone']) ? $customer_data['phone'] : 'N/A') . '</li>
            </ul>
            <h3>Payload Parameters</h3>
            <pre style="background: #f5f5f5; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 12px; white-space: pre-wrap;">' . esc_html($quote_text) . '</pre>
        </div>
    </div>';

    $sent_customer = wp_mail($customer_email, $subject, $customer_html, $headers);
    $sent_admin    = wp_mail($admin_email, $admin_subject, $admin_html, $headers);

    if ( $sent_customer || $sent_admin ) {
        wp_send_json_success('Sent');
    } else {
        wp_send_json_error('Mail delivery failed.');
    }
}
