// ═══════════════════════════════════════════════════════════════
// BN DULAY TRUX - RATE ENGINE
// +20% on base rates | FSC 32%
// Weight matrix + dedicated truck rates + Island routes
// ═══════════════════════════════════════════════════════════════

const FUEL_SURCHARGE = 0.32;

const WEIGHT_LIMITS = {
  '5_ton': 15000,
  '10_ton': 28000,
  'tandem': 48000,
  'tridem': 60000,
  'super_b': 85000
};

// City → Zone (from Russel Metal sheet)
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

// Weight matrix (+20% already applied)
// [min, max, [Z1, Z2, Z3, Z4, Z5, Z6, Z7]]
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

// Dedicated truck flat rates (+20% already applied)
const DEDICATED_RATES = {
  '5_ton': {
    'Aldergrove': 144, 'Burnaby': 144, 'Coquitlam': 144, 'Delta': 144,
    'Langley': 144, 'Maple Ridge': 144, 'New Westminster': 144, 'Richmond': 144,
    'Surrey': 144, 'Abbotsford': 144, 'Vancouver': 144, 'Port Coquitlam': 144, 'Port Moody': 144,
    'Chilliwack': 180, 'Mission': 180, 'North Vancouver': 168, 'Rosedale': 180, 'West Vancouver': 168
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
    'Surrey': 342, 'Abbotsford': 402, 'Mission': 402, 'Vancouver': 402,
    'Port Coquitlam': 342, 'Port Moody': 342,
    'Chilliwack': 462, 'North Vancouver': 462, 'Rosedale': 462, 'West Vancouver': 462
  },
  'tridem': {
    'Aldergrove': 420, 'Burnaby': 360, 'Coquitlam': 360, 'Delta': 360,
    'Langley': 360, 'Maple Ridge': 360, 'New Westminster': 360, 'Richmond': 360,
    'Surrey': 360, 'Abbotsford': 420, 'Mission': 420, 'Vancouver': 420,
    'Port Coquitlam': 360, 'Port Moody': 360,
    'Chilliwack': 480, 'North Vancouver': 480, 'Rosedale': 480, 'West Vancouver': 480
  },
  'super_b': {
    'Aldergrove': 540, 'Burnaby': 420, 'Coquitlam': 420, 'Delta': 420,
    'Langley': 420, 'Maple Ridge': 420, 'New Westminster': 420, 'Richmond': 420,
    'Surrey': 420, 'Abbotsford': 540, 'Vancouver': 540,
    'Port Coquitlam': 420, 'Port Moody': 420,
    'North Vancouver': 540, 'West Vancouver': 540,
    'Chilliwack': 630, 'Mission': 630, 'Rosedale': 630
  }
};

const CUSTOM_QUOTE_TRUCKS = ['chassis', 'curtain_van', 'dry_van', 'rolltite', 'not_sure'];

function calculateLocalQuote(data) {
  const { pickup_city, delivery_city, weight, truck_type } = data;

  if (!pickup_city || !delivery_city) return { error: "Please select both cities." };
  if (!weight || weight <= 0) return { error: "Please enter a valid weight." };
  if (!truck_type) return { error: "Please select a truck type." };

  if (CUSTOM_QUOTE_TRUCKS.includes(truck_type)) {
    return {
      customQuote: true,
      tier: "Custom Quote Required",
      truck: truck_type,
      pickup: pickup_city,
      delivery: delivery_city,
      weight: weight
    };
  }

  const maxWeightLimit = WEIGHT_LIMITS[truck_type];
  if (maxWeightLimit && weight > maxWeightLimit) {
    return {
      customQuote: true,
      overweight: true,
      tier: "Custom Quote Required (Overweight)",
      truck: truck_type,
      pickup: pickup_city,
      delivery: delivery_city,
      weight: weight,
      weightLimit: maxWeightLimit
    };
  }

  let baseRate = 0;

  // Island routes (Victoria / Nanaimo) — +20% already applied
  if (
    pickup_city === 'Victoria' || pickup_city === 'Nanaimo' ||
    delivery_city === 'Victoria' || delivery_city === 'Nanaimo'
  ) {
    if (truck_type === 'super_b') {
      baseRate = 2760; // 2300 + 20%
    } else if (truck_type === 'tridem' || truck_type === 'tandem' || weight > 30000) {
      baseRate = 2400; // 2000 + 20%
    } else {
      baseRate = 1620; // 1350 + 20%
    }
  } else {
    // Mainland BC
    const pickupZone = CITY_ZONES[pickup_city];
    const deliveryZone = CITY_ZONES[delivery_city];

    // 1) Weight matrix rate (if <= 30,000 lbs)
    let matrixRate = 0;
    if (weight <= 30000 && pickupZone && deliveryZone && pickupZone !== 'ISLAND' && deliveryZone !== 'ISLAND') {
      const targetZone = Math.max(pickupZone, deliveryZone);
      const bracket = WEIGHT_MATRIX.find(row => weight >= row[0] && weight <= row[1]);
      if (bracket) {
        matrixRate = bracket[2][targetZone - 1];
      }
    }

    // 2) Dedicated truck flat rate
    let equipmentRate = 0;
    const truckTable = DEDICATED_RATES[truck_type];
    if (truckTable) {
      const pRate = truckTable[pickup_city];
      const dRate = truckTable[delivery_city];
      if (pRate !== undefined && dRate !== undefined) {
        equipmentRate = Math.max(pRate, dRate);
      } else if (pRate !== undefined) {
        equipmentRate = pRate;
      } else if (dRate !== undefined) {
        equipmentRate = dRate;
      }
    }

    // Use the higher of matrix vs dedicated
    baseRate = Math.max(matrixRate || 0, equipmentRate || 0);
  }

  if (!baseRate || baseRate <= 0) {
    return { error: "Selected city pair needs a custom quote. Please call dispatch." };
  }

  const fuelSurcharge = baseRate * FUEL_SURCHARGE;
  const total = baseRate + fuelSurcharge;

  return {
    tier: "Local Delivery",
    pickup: pickup_city,
    delivery: delivery_city,
    weight: weight,
    truck: truck_type,
    truck_type: truck_type,
    baseRate: baseRate.toFixed(2),
    subtotal: baseRate.toFixed(2),
    fuelSurcharge: fuelSurcharge.toFixed(2),
    fuelSurchargePercent: (FUEL_SURCHARGE * 100).toFixed(0),
    total: total.toFixed(2)
  };
}
