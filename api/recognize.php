<?php
/**
 * AI Image Recognition API Endpoint
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../includes/init.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// Check for uploaded image
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    respond(['success' => false, 'message' => 'No image uploaded or upload error.']);
}

$file = $_FILES['image'];

// Validate image
if ($file['size'] > AI_MAX_IMAGE_SIZE) {
    respond(['success' => false, 'message' => 'Image is too large. Maximum size is 2MB.']);
}

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
    respond(['success' => false, 'message' => 'Invalid image type. Only JPG, PNG, and WEBP are allowed.']);
}

// Get AI settings
$aiSettings = get_ai_settings();
$provider = $aiSettings['provider'] ?? 'gemini';
$apiKey = $aiSettings['api_key'] ?? '';
$model = $aiSettings['model'] ?? '';

if (empty($apiKey)) {
    respond(['success' => false, 'message' => 'AI service is not configured. Please contact the administrator.']);
}

// Get product catalog
$catalog = get_catalog_for_ai();

if (empty($catalog)) {
    respond(['success' => false, 'message' => 'Product catalog is empty.']);
}

// Prepare catalog text for prompt
$catalogText = "Product Catalog:\n";
foreach ($catalog as $product) {
    $catalogText .= "- SKU: " . $product['sku'] . "\n";
    $catalogText .= "  Name: " . $product['name'] . "\n";
    if (!empty($product['detect_keywords'])) {
        $catalogText .= "  Keywords: " . $product['detect_keywords'] . "\n";
    }
    if (!empty($product['description'])) {
        $catalogText .= "  Description: " . $product['description'] . "\n";
    }
    $catalogText .= "\n";
}

// Prepare image for AI
$imageResult = prepare_image_for_ai($file['tmp_name']);
if (!$imageResult['success']) {
    respond(['success' => false, 'message' => $imageResult['message']]);
}

// Build AI prompt
$prompt = "You are a retail product recognition assistant.\n\n" .
          "Analyze the provided product image.\n\n" .
          "Identify the product based only on the provided image and the supplied product catalog.\n\n" .
          $catalogText .
          "Return JSON only with the following format:\n" .
          "{\n" .
          "  \"sku\": \"SKU from catalog or null if not found\",\n" .
          "  \"product_name\": \"Full product name or null if not found\",\n" .
          "  \"confidence\": 0.0 to 1.0,\n" .
          "  \"reason\": \"Brief explanation of your identification\"\n" .
          "}\n\n" .
          "Rules:\n" .
          "- Do NOT invent prices\n" .
          "- Do NOT invent SKU values that don't exist in the catalog\n" .
          "- If you cannot confidently identify a product, return null for SKU\n" .
          "- Use only the catalog information provided\n";

// Call AI
$messages = [
    ['role' => 'user', 'content' => [
        ['type' => 'text', 'text' => $prompt],
        [
            'type' => 'image_url',
            'image_url' => [
                'url' => 'data:' . $imageResult['mime'] . ';base64,' . $imageResult['data']
            ]
        ]
    ]]
];

$aiResult = ai_chat($messages);

if (!$aiResult['success']) {
    respond(['success' => false, 'message' => 'AI recognition failed: ' . $aiResult['message']]);
}

// Parse AI response
$aiResponse = parseAiResponse($aiResult['response']['text'] ?? '');

if (!$aiResponse) {
    respond(['success' => false, 'message' => 'Invalid AI response format.']);
}

// Match with catalog
$matchedProduct = matchProductWithCatalog($aiResponse, $catalog);

if (!$matchedProduct) {
    respond([
        'success' => true,
        'found' => false,
        'message' => 'Product not found in catalog. The AI could not match the image to any known product.'
    ]);
}

// Return product from database (price from DB, not AI)
$formattedProduct = [
    'id' => (int)$matchedProduct['id'],
    'sku' => $matchedProduct['sku'],
    'barcode' => $matchedProduct['barcode'],
    'name' => $matchedProduct['name'],
    'price' => number_format((float)$matchedProduct['price'], 2, '.', ''),
    'description' => $matchedProduct['description'],
    'image' => $matchedProduct['image_path'],
    'confidence' => $aiResponse['confidence'] ?? 0.0
];

respond([
    'success' => true,
    'found' => true,
    'product' => $formattedProduct,
    'match_method' => 'AI Recognition',
    'reason' => $aiResponse['reason'] ?? ''
]);

/**
 * Parse AI JSON response
 */
function parseAiResponse(string $text): ?array {
    // Try to extract JSON from response
    $jsonMatch = null;
    
    // Look for JSON object in the text
    if (preg_match('/\{[^{}]*"sku"[^{}]*\}/', $text, $jsonMatch)) {
        $jsonStr = $jsonMatch[0];
    } elseif (preg_match('/```json\s*([\s\S]*?)\s*```/', $text, $jsonMatch)) {
        $jsonStr = $jsonMatch[1];
    } else {
        $jsonStr = $text;
    }
    
    $data = json_decode($jsonStr, true);
    
    if (!$data || !isset($data['sku'])) {
        return null;
    }
    
    // Normalize confidence
    $confidence = floatval($data['confidence'] ?? 0.0);
    if ($confidence > 1.0) {
        $confidence = $confidence / 100; // Convert percentage to decimal
    }
    
    return [
        'sku' => trim($data['sku'] ?? ''),
        'product_name' => trim($data['product_name'] ?? ''),
        'confidence' => $confidence,
        'reason' => trim($data['reason'] ?? '')
    ];
}

/**
 * Match AI response with catalog
 */
function matchProductWithCatalog(array $aiResponse, array $catalog): ?array {
    $sku = trim($aiResponse['sku'] ?? '');
    
    // Method 1: Match by SKU
    if (!empty($sku)) {
        foreach ($catalog as $product) {
            if (strcasecmp($product['sku'], $sku) === 0) {
                return $product;
            }
        }
    }
    
    // Method 2: Match by product name
    $aiName = trim($aiResponse['product_name'] ?? '');
    if (!empty($aiName)) {
        foreach ($catalog as $product) {
            if (stripos($product['name'], $aiName) !== false || 
                stripos($aiName, $product['name']) !== false) {
                return $product;
            }
        }
    }
    
    // Method 3: Match by keywords
    foreach ($catalog as $product) {
        $keywords = explode(',', $product['detect_keywords'] ?? '');
        foreach ($keywords as $keyword) {
            $keyword = trim(strtolower($keyword));
            if (empty($keyword)) continue;
            
            $productName = strtolower($product['name']);
            $productDesc = strtolower($product['description'] ?? '');
            $aiText = strtolower($aiResponse['product_name'] ?? '') . ' ' . 
                      strtolower($aiResponse['reason'] ?? '');
            
            if (strpos($aiText, $keyword) !== false && 
                (strpos($productName, $keyword) !== false || 
                 strpos($productDesc, $keyword) !== false)) {
                return $product;
            }
        }
    }
    
    return null;
}

/**
 * Send JSON response
 */
function respond(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}
