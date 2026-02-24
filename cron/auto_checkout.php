<?php
/**
 * Auto-Checkout Script
 * 
 * Automatically checks out all visitors who forgot to checkout by end of day.
 * This script should be scheduled to run at 23:59 daily.
 * 
 * Usage:
 *   - Windows Task Scheduler: php D:\laragon\www\BukuTamu\cron\auto_checkout.php
 *   - Linux Cron: 59 23 * * * php /path/to/BukuTamu/cron/auto_checkout.php
 *   - Or call via browser/API: http://localhost/BukuTamu/cron/auto_checkout.php?key=YOUR_SECRET_KEY
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Security: Allow CLI execution or require secret key for web access
$isCli = (php_sapi_name() === 'cli');
if (!$isCli) {
    $secretKey = 'bukutamu_auto_checkout_2026'; // Change this to your own secret key
    if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
        http_response_code(403);
        die('Unauthorized');
    }
}

$logFile = __DIR__ . '/../logs/auto_flag.log';

try {
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');
    
    // Find all active visits for today that haven't checked out
    $sql = "SELECT id, nama, nomor_identitas, no_pek FROM visits 
            WHERE visit_date = :date 
            AND status = 'MASUK'";
    
    $activeVisits = getAll($sql, ['date' => $today]);
    
    $count = count($activeVisits);
    
    if ($count === 0) {
        $message = "[{$now}] No active visits to auto-checkout.\n";
        file_put_contents($logFile, $message, FILE_APPEND);
        if ($isCli) echo $message;
        exit;
    }
    
    // Auto-checkout all active visits
    $sql = "UPDATE visits 
            SET status = 'SELESAI', 
                jam_keluar = :checkout_time,
                is_flagged = TRUE,
                flag_note = 'Auto-checkout oleh sistem pada 23:59 (tidak absen keluar)',
                updated_at = :updated_at
            WHERE visit_date = :date 
            AND status = 'MASUK'";
    
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'checkout_time' => $now,
        'updated_at' => $now,
        'date' => $today
    ]);
    
    $affected = $stmt->rowCount();
    
    // Log the results
    $message = "[{$now}] Auto-checkout completed: {$affected} visitors auto-checked out.\n";
    foreach ($activeVisits as $visit) {
        $noPek = $visit['no_pek'] ?? '-';
        $message .= "  - {$visit['nama']} (NO.PEK: {$noPek}, ID Card: {$visit['nomor_identitas']})\n";
    }
    
    file_put_contents($logFile, $message, FILE_APPEND);
    
    if ($isCli) {
        echo $message;
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => "Auto-checkout completed: {$affected} visitors",
            'count' => $affected
        ]);
    }
    
} catch (Exception $e) {
    $errorMsg = "[" . date('Y-m-d H:i:s') . "] Auto-checkout ERROR: " . $e->getMessage() . "\n";
    file_put_contents($logFile, $errorMsg, FILE_APPEND);
    
    if ($isCli) {
        echo $errorMsg;
        exit(1);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
