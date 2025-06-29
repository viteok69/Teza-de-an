<?php

// Site configuration
define('SITE_NAME', 'Ghidul Tinerilor Chișinău');
define('SITE_URL', 'http://localhost/chisinau-youth-guide');
define('UPLOAD_PATH', 'uploads/');

// Include database connection
require_once __DIR__ . '/database.php';

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
