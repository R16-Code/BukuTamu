<?php
// Script untuk cek dan perbaiki admin user
require_once __DIR__ . '/config/database.php';

echo "=== CEK DATABASE ===\n\n";

try {
    // Test koneksi
    echo "✓ Koneksi database berhasil\n";
    echo "Database: " . DB_NAME . "\n\n";
    
    // Cek apakah tabel admin_users ada
    $checkTable = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    if ($checkTable->rowCount() == 0) {
        echo "❌ Tabel admin_users belum ada!\n";
        echo "Silakan import database.sql terlebih dahulu.\n";
        exit;
    }
    echo "✓ Tabel admin_users ada\n\n";
    
    // Cek apakah admin user ada
    $stmt = $pdo->query("SELECT * FROM admin_users WHERE username = 'admin'");
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo "❌ User admin belum ada. Membuat user admin...\n";
        
        // Buat password hash baru
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        
        $pdo->exec("INSERT INTO admin_users (username, password, nama) VALUES ('admin', '$passwordHash', 'Administrator')");
        
        echo "✓ User admin berhasil dibuat!\n";
        echo "   Username: admin\n";
        echo "   Password: admin123\n";
    } else {
        echo "✓ User admin sudah ada\n";
        echo "   Username: " . $admin['username'] . "\n";
        echo "   Nama: " . $admin['nama'] . "\n\n";
        
        // Test password verification
        echo "=== TEST PASSWORD ===\n";
        $testPassword = 'admin123';
        $isValid = password_verify($testPassword, $admin['password']);
        
        if ($isValid) {
            echo "✓ Password 'admin123' VALID!\n";
        } else {
            echo "❌ Password 'admin123' TIDAK VALID!\n";
            echo "Regenerate password hash...\n";
            
            // Generate password baru
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->exec("UPDATE admin_users SET password = '$newHash' WHERE username = 'admin'");
            
            echo "✓ Password berhasil di-reset!\n";
            echo "   Username: admin\n";
            echo "   Password: admin123\n";
        }
    }
    
    echo "\n=== SELESAI ===\n";
    echo "Silakan coba login lagi di: http://localhost/BukuTamu/admin/login.php\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nPastikan:\n";
    echo "1. MySQL sudah running\n";
    echo "2. Database 'bukutamu' sudah dibuat\n";
    echo "3. File database.sql sudah di-import\n";
}
