<?php
/**
 * Configuration file for AI Smart Shopping Assistant
 */

// Application name
define('APP_NAME', 'AI Smart Shopping Assistant');

// Auto-detect application URL
if (!defined('APP_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $scriptDirectory = basename(rtrim($script, '/\\'));
    if (in_array(strtolower($scriptDirectory), ['admin', 'api', 'includes'], true)) {
        $script = dirname($script);
    }
    if ($script === '/') {
        define('APP_URL', $protocol . $host);
    } else {
        // Remove trailing slash for consistency
        $script = rtrim($script, '/');
        define('APP_URL', $protocol . $host . $script);
    }
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ai_shopping_assistant');
define('DB_USER', 'root');
define('DB_PASS', '123456');
define('DB_CHARSET', 'utf8mb4');

// Application paths
define('BASE_PATH', dirname(__DIR__));
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('PRODUCTS_UPLOAD_PATH', UPLOADS_PATH . '/products');
define('CACHE_UPLOAD_PATH', UPLOADS_PATH . '/cache');

// Image upload settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('IMAGE_MAX_WIDTH', 1920);
define('IMAGE_MAX_HEIGHT', 1920);
define('IMAGE_COMPRESS_QUALITY', 85);

// Allowed image MIME types
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/jpg',
    'image/png',
    'image/webp'
]);

// Allowed image extensions
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Session lifetime (24 hours)
define('SESSION_LIFETIME', 86400);

// Default page size for catalog
define('PAGE_SIZE', 20);

// AI settings
define('AI_TIMEOUT', 30); // seconds
define('AI_MAX_IMAGE_SIZE', 2000000); // 2MB max for AI processing
define('AI_MAX_IMAGE_DIMENSION', 1024); // Max dimension for AI images
