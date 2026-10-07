<?php
/**
 * Settings helper functions
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    http_response_code(403);
    exit('Access denied');
}

/**
 * Get setting value
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function get_setting(string $key, $default = null) {
    $pdo = getDBConnection();
    if ($pdo === null) return $default;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        
        if ($value === false || $value === null) {
            return $default;
        }
        
        return $value;
    } catch (Exception $e) {
        error_log('Get setting error: ' . $e->getMessage());
        return $default;
    }
}

/**
 * Set setting value
 * @param string $key
 * @param mixed $value
 * @return bool
 */
function set_setting(string $key, $value): bool {
    $pdo = getDBConnection();
    if ($pdo === null) return false;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
        return true;
    } catch (Exception $e) {
        error_log('Set setting error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get all AI settings
 * @return array
 */
function get_ai_settings(): array {
    $model = get_setting('gemini_model', 'gemini-3.8-flash');
    if ($model === 'gemini-2.0-flash') {
        $model = 'gemini-3.8-flash';
        set_setting('gemini_model', $model);
    }

    return [
        'provider' => get_setting('ai_provider', 'gemini'),
        'api_url' => get_setting('gemini_api_url', ''),
        'api_key' => get_setting('gemini_api_key', ''),
        'model' => $model,
        'fallback_models' => get_setting('gemini_fallback_models', ''),
    ];
}

/**
 * Get masked API key for display
 * @param string $key
 * @return string
 */
function mask_api_key(string $key): string {
    if (strlen($key) <= 4) {
        return str_repeat('*', strlen($key));
    }
    return str_repeat('*', strlen($key) - 4) . substr($key, -4);
}
