<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    // Validate input
    if (empty($_POST['username']) || empty($_POST['password'])) {
        jsonResponse(false, 'Username dan password wajib diisi!');
    }
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Find admin user
    $admin = getRow("SELECT * FROM admin_users WHERE username = :username", [
        'username' => $username
    ]);
    
    if (!$admin) {
        jsonResponse(false, 'Username atau password salah!');
    }
    
    // Verify password
    if (!password_verify($password, $admin['password'])) {
        jsonResponse(false, 'Username atau password salah!');
    }
    
    // Set session
    $_SESSION[ADMIN_SESSION_KEY] = true;
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_nama'] = $admin['nama'];
    
    jsonResponse(true, 'Login berhasil! Mengalihkan...', [
        'redirect' => 'dashboard.php'
    ]);
    
} catch (Exception $e) {
    error_log('Login Error: ' . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem. Silakan coba lagi.');
}
