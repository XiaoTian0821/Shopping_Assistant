<?php
/**
 * AI Connection Test Endpoint
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../includes/init.php';

// Only allow admin access
if (!is_admin()) {
    respond(['success' => false, 'message' => 'Unauthorized.']);
}

// Test AI connection
$result = test_ai_connection();
respond($result);

function respond(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}
