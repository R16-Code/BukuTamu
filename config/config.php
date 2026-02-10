<?php
// ===================================
// APPLICATION CONFIGURATION
// ===================================

// Timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'bukutamu');

// Application Settings
define('APP_NAME', 'PEPC Visitor Log');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/BukuTamu'); // Sesuaikan dengan URL Anda

// Admin Session Key
define('ADMIN_SESSION_KEY', 'buku_tamu_admin');

// Error Reporting (Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Error Reporting (Production - uncomment untuk production)
// error_reporting(0);
// ini_set('display_errors', 0);

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// Generate CSRF Token if not exists
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
