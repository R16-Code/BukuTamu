<?php
/**
 * Auto-Flag Script
 * Dijalankan melalui cron job setiap hari jam 23:59
 * Atau bisa dipanggil manual oleh admin
 * 
 * Fungsi: Menandai semua kunjungan dengan status MASUK (belum checkout)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Log execution
$logFile = __DIR__ . '/../logs/auto_flag.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
}

try {
    logMessage("=== Auto-Flag Script Started ===");
    
    $today = date('Y-m-d');
    
    // Find all MASUK records from today
    $sql = "SELECT COUNT(*) as total FROM visits 
            WHERE visit_date = :date 
            AND status = 'MASUK' 
            AND is_flagged = 0";
    
    $result = getRow($sql, ['date' => $today]);
    $totalUnflagged = $result['total'];
    
    logMessage("Found {$totalUnflagged} unflagged MASUK records for {$today}");
    
    if ($totalUnflagged > 0) {
        // Update all MASUK records from today to flagged
        $updateSql = "UPDATE visits 
                      SET is_flagged = 1,
                          flag_note = 'Tidak ada absen keluar - Auto flagged at 23:59'
                      WHERE visit_date = :date 
                      AND status = 'MASUK' 
                      AND is_flagged = 0";
        
        $stmt = executeQuery($updateSql, ['date' => $today]);
        $updated = $stmt->rowCount();
        
        logMessage("Successfully flagged {$updated} records");
        
        // If run via web (admin manual trigger)
        if (php_sapi_name() !== 'cli') {
            jsonResponse(true, "Berhasil flag {$updated} kunjungan yang belum checkout", [
                'total_flagged' => $updated
            ]);
        }
    } else {
        logMessage("No records to flag");
        
        if (php_sapi_name() !== 'cli') {
            jsonResponse(true, "Tidak ada kunjungan yang perlu di-flag", [
                'total_flagged' => 0
            ]);
        }
    }
    
    logMessage("=== Auto-Flag Script Completed Successfully ===\n");
    
} catch (Exception $e) {
    $errorMsg = "Error: " . $e->getMessage();
    logMessage($errorMsg);
    logMessage("=== Auto-Flag Script Failed ===\n");
    
    if (php_sapi_name() !== 'cli') {
        jsonResponse(false, 'Terjadi kesalahan saat menjalankan auto-flag.');
    }
}
