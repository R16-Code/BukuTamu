<?php
// HELPER FUNCTIONS

require_once __DIR__ . '/../config/database.php';

/**
 * Check if admin is authenticated
 *
 * @return bool
 */
function checkAuthAdmin() {
    return isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] === true;
}

/**
 * Require admin authentication (redirect if not logged in)
 */
function requireAdmin() {
    if (!checkAuthAdmin()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Format datetime ke format Indonesia
 *
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime) {
    if (empty($datetime)) {
        return '-';
    }
    
    $timestamp = strtotime($datetime);
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    $day = $days[date('w', $timestamp)];
    $date = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return "{$day}, {$date} {$month} {$year} - {$time} WIB";
}

/**
 * Format date only (tanpa jam)
 *
 * @param string $date
 * @return string
 */
function formatDate($date) {
    if (empty($date)) {
        return '-';
    }
    
    $timestamp = strtotime($date);
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "{$day} {$month} {$year}";
}

/**
 * Format time only
 *
 * @param string $datetime
 * @return string
 */
function formatTime($datetime) {
    if (empty($datetime)) {
        return '-';
    }
    
    return date('H:i', strtotime($datetime)) . ' WIB';
}

/**
 * Check if identity already has active entry today
 *
 * @param string $noPek NO.PEK/NIK/SIM/PASPORT
 * @param string $date Date in Y-m-d format
 * @return array|false Return row if exists, false if not
 */
function checkDuplicateEntry($noPek, $date) {
    $sql = "SELECT * FROM visits 
            WHERE no_pek = :no_pek 
            AND visit_date = :date 
            AND status = 'MASUK'
            LIMIT 1";
    
    return getRow($sql, [
        'no_pek' => $noPek,
        'date' => $date
    ]);
}

/**
 * Check if NO. ID CARD already has active entry today (belum checkout)
 *
 * @param string $nomorIdentitas NO. ID CARD
 * @param string $date Date in Y-m-d format
 * @return array|false Return row if exists, false if not
 */
function checkDuplicateIdCard($nomorIdentitas, $date) {
    $sql = "SELECT * FROM visits 
            WHERE nomor_identitas = :nomor_identitas 
            AND visit_date = :date 
            AND status = 'MASUK'
            LIMIT 1";
    
    return getRow($sql, [
        'nomor_identitas' => $nomorIdentitas,
        'date' => $date
    ]);
}

/**
 * Get visit statistics for a specific date
 *
 * @param string $date Date in Y-m-d format
 * @return array
 */
function getVisitStats($date) {
    // Total hari ini
    $totalSql = "SELECT COUNT(*) as total FROM visits WHERE visit_date = :date";
    $total = getRow($totalSql, ['date' => $date])['total'] ?? 0;
    
    // Masih di dalam (MASUK)
    $insideSql = "SELECT COUNT(*) as inside FROM visits 
                  WHERE visit_date = :date AND status = 'MASUK'";
    $inside = getRow($insideSql, ['date' => $date])['inside'] ?? 0;
    
    // Sudah keluar (SELESAI)
    $exitedSql = "SELECT COUNT(*) as exited FROM visits 
                  WHERE visit_date = :date AND status = 'SELESAI'";
    $exited = getRow($exitedSql, ['date' => $date])['exited'] ?? 0;
    
    return [
        'total' => $total,
        'inside' => $inside,
        'exited' => $exited
    ];
}

/**
 * Sanitize output untuk HTML
 *
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Send JSON response
 *
 * @param bool $success
 * @param string $message
 * @param array $data
 */
function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Validate CSRF token
 *
 * @param string $token
 * @return bool
 */
function validateCSRF($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get CSRF token
 *
 * @return string
 */
function getCSRFToken() {
    return $_SESSION[CSRF_TOKEN_NAME] ?? '';
}

/**
 * Auto-checkout pengunjung dari hari-hari sebelumnya yang masih berstatus MASUK.
 * Fungsi ini dipanggil otomatis setiap kali functions.php di-load (setiap page load).
 * Aman untuk dipanggil berkali-kali (idempotent).
 */
function autoCheckoutPreviousDays() {
    global $pdo;
    
    if (!$pdo) return;
    
    try {
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        // Cek apakah ada pengunjung dari hari sebelumnya yang masih MASUK
        $checkSql = "SELECT COUNT(*) as total FROM visits 
                     WHERE visit_date < :today AND status = 'MASUK'";
        $check = getRow($checkSql, ['today' => $today]);
        
        // Jika tidak ada, skip (query ringan)
        if (!$check || (int)$check['total'] === 0) {
            return;
        }
        
        // Ambil data untuk logging sebelum update
        $detailSql = "SELECT id, nama, no_pek, nomor_identitas, visit_date FROM visits 
                      WHERE visit_date < :today AND status = 'MASUK'";
        $activeVisits = getAll($detailSql, ['today' => $today]);
        
        // Auto-checkout semua
        $updateSql = "UPDATE visits 
                      SET status = 'SELESAI', 
                          jam_keluar = CONCAT(visit_date, ' 23:59:00'),
                          is_flagged = TRUE,
                          flag_note = 'Auto-checkout oleh sistem (tidak absen keluar)',
                          updated_at = :updated_at
                      WHERE visit_date < :today 
                      AND status = 'MASUK'";
        
        $stmt = $pdo->prepare($updateSql);
        $stmt->execute([
            'updated_at' => $now,
            'today' => $today
        ]);
        
        $affected = $stmt->rowCount();
        
        // Log hasil
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/auto_checkout.log';
        $message = "[{$now}] Auto-checkout (page load): {$affected} pengunjung dari hari sebelumnya.\n";
        foreach ($activeVisits as $visit) {
            $noPek = $visit['no_pek'] ?? '-';
            $message .= "  - {$visit['nama']} (NO.PEK: {$noPek}, Tanggal: {$visit['visit_date']})\n";
        }
        
        file_put_contents($logFile, $message, FILE_APPEND);
        
    } catch (Exception $e) {
        // Jangan sampai error auto-checkout mengganggu halaman utama
        error_log("Auto-checkout error: " . $e->getMessage());
    }
}

// Jalankan auto-checkout setiap kali functions.php di-load
autoCheckoutPreviousDays();
