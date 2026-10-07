<?php
/**
 * Installation check API
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $response['message'] = 'Invalid data.';
    echo json_encode($response);
    exit;
}

$action = $data['action'] ?? '';
unset($data['action']);

try {
    // Create connection without database
    $pdo = new PDO(
        'mysql:host=' . ($data['host'] ?? 'localhost') . ';charset=utf8mb4',
        $data['user'] ?? 'root',
        $data['pass'] ?? '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Create database if needed
    $dbName = $data['name'] ?? 'ai_shopping_assistant';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $pdo->quoteIdentifier($dbName) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . $pdo->quoteIdentifier($dbName) . "`");
    
    if ($action === 'create_tables') {
        // Read and execute schema
        $schema = file_get_contents(__DIR__ . '/sql/schema.sql');
        if ($schema === false) {
            throw new Exception('Failed to read schema file.');
        }
        
        // Remove USE statement as we already selected the database
        $schema = preg_replace('/^USE[^;]+;/', '', $schema, 1);
        
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Tables created successfully.';
    }
    
    if ($action === 'insert_data') {
        $seed = file_get_contents(__DIR__ . '/sql/seed.sql');
        if ($seed === false) {
            throw new Exception('Failed to read seed file.');
        }
        
        // Remove USE statement
        $seed = preg_replace('/^USE[^;]+;/', '', $seed, 1);
        
        // Only insert if checkboxes are checked
        if (!($data['insertAdmin'] ?? true)) {
            $seed = preg_replace('/INSERT INTO admins.*?;/', '', $seed, 1);
        }
        if (!($data['insertProducts'] ?? true)) {
            $seed = preg_replace('/INSERT INTO products.*?;/s', '', $seed, 1);
        }
        if (!($data['insertSettings'] ?? true)) {
            $seed = preg_replace('/INSERT INTO settings.*?;/', '', $seed, 1);
        }
        
        $statements = array_filter(array_map('trim', explode(';', $seed)));
        foreach ($statements as $statement) {
            if (!empty($statement) && strpos($statement, 'INSERT') === 0) {
                $pdo->exec($statement);
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Sample data inserted successfully.';
    }
    
    if (empty($action)) {
        $response['success'] = true;
        $response['message'] = 'Database connection successful.';
    }
    
} catch (Exception $e) {
    error_log('Installation error: ' . $e->getMessage());
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
