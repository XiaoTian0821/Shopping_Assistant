<?php
/**
 * Database connection using PDO with error handling
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    http_response_code(403);
    exit('Access denied');
}

/**
 * Get PDO database connection
 * @return PDO|null Returns null on failure
 */
function getDBConnection(): ?PDO {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log error but don't expose details to user
        error_log('Database connection error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Check if database is connected
 * @return bool
 */
function isDBConnected(): bool {
    return getDBConnection() !== null;
}
