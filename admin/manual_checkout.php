<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin authentication
requireAdmin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    // Validate input
    if (empty($_POST['visit_id'])) {
        jsonResponse(false, 'Visit ID wajib diisi!');
    }
    
    $visitId = $_POST['visit_id'];
    
    // Verify visit exists and is in MASUK status
    $visit = getRow("SELECT * FROM visits WHERE id = :id", ['id' => $visitId]);
    
    if (!$visit) {
        jsonResponse(false, 'Data kunjungan tidak ditemukan!');
    }
    
    if ($visit['status'] !== 'MASUK') {
        jsonResponse(false, 'Kunjungan ini sudah selesai (sudah checkout).');
    }
    
    // Update visit with manual checkout
    $updated = update('visits', 
        [
            'jam_keluar' => date('Y-m-d H:i:s'),
            'status' => 'SELESAI',
            'is_flagged' => 0,
            'flag_note' => 'Checkout manual oleh admin'
        ],
        'id = :id',
        ['id' => $visitId]
    );
    
    if ($updated > 0) {
        jsonResponse(true, 'Checkout manual berhasil untuk ' . $visit['nama'], [
            'jam_keluar' => formatTime(date('Y-m-d H:i:s'))
        ]);
    } else {
        jsonResponse(false, 'Gagal melakukan checkout manual.');
    }
    
} catch (Exception $e) {
    error_log('Manual Checkout Error: ' . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem. Silakan coba lagi.');
}
