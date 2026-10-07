<?php
/**
 * Logout handler
 */
require_once __DIR__ . '/includes/init.php';

logout_user();
header('Location: ' . APP_URL . '/login.php');
exit;
