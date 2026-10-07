<?php
/**
 * Authentication helper functions
 */

// Prevent direct access
if (!defined('APP_NAME')) {
    http_response_code(403);
    exit('Access denied');
}

/**
 * Get current user data
 * @return array|null
 */
function current_user(): ?array {
    return $_SESSION['admin'] ?? null;
}

/**
 * Check if current user is admin
 * @return bool
 */
function is_admin(): bool {
    return current_user() !== null;
}

/**
 * Require admin authentication, redirect if not logged in
 */
function require_admin(): void {
    if (!is_admin()) {
        redirect_to(LOGIN_URL);
    }
}

/**
 * Attempt login
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function attempt_login(string $email, string $password): array {
    $pdo = getDBConnection();
    
    if ($pdo === null) {
        return ['success' => false, 'message' => 'Database connection error.'];
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, email, password, name FROM admins WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'name' => $admin['name']
            ];
            $_SESSION['last_activity'] = time();
            return ['success' => true, 'message' => 'Login successful.'];
        }
        
        return ['success' => false, 'message' => 'Invalid email or password.'];
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Login failed. Please try again.'];
    }
}

/**
 * Logout user
 */
function logout_user(): void {
    session_unset();
    session_destroy();
}

/**
 * Generate CSRF token
 * @return string
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function csrf_verify(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Get CSRF token HTML input
 * @return string
 */
function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Redirect to URL
 * @param string $url
 */
function redirect_to(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Get login URL
 */
define('LOGIN_URL', APP_URL . '/login.php');
