<?php
/**
 * Barcode API Endpoint
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

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);
$barcode = trim($input['barcode'] ?? '');

if (empty($barcode)) {
    respond(['success' => false, 'message' => 'Barcode is required.']);
}

// Normalize barcode (remove non-numeric characters except for special formats)
$normalizedBarcode = preg_replace('/[^0-9A-Za-z\-]/', '', $barcode);

if (empty($normalizedBarcode)) {
    respond(['success' => false, 'message' => 'Invalid barcode format.']);
}

// Search for product
$product = get_product_by_barcode($normalizedBarcode);

if ($product) {
    // Format product for response
    $responseProduct = [
        'id' => (int)$product['id'],
        'sku' => $product['sku'],
        'barcode' => $product['barcode'],
        'name' => $product['name'],
        'price' => number_format((float)$product['price'], 2, '.', ''),
        'description' => $product['description'],
        'image' => $product['image_path']
    ];
    
    respond([
        'success' => true,
        'found' => true,
        'product' => $responseProduct
    ]);
} else {
    respond([
        'success' => true,
        'found' => false,
        'message' => 'Product not found in catalog.'
    ]);
}

/**
 * Send JSON response
 */
function respond(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}
