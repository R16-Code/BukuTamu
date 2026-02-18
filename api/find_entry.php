<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    $noPek = isset($_POST['no_pek']) ? trim($_POST['no_pek']) : '';
    $nomorIdentitas = isset($_POST['nomor_identitas']) ? trim($_POST['nomor_identitas']) : '';
    
    if (empty($_POST['no_pek']) || empty($_POST['nomor_identitas'])) {
        jsonResponse(false, 'NO.PEK/NIK/SIM/PASPORT dan NO. ID CARD wajib diisi!');
    }
    
    $today = date('Y-m-d');
    $visit = null;
    
    // Search by no_pek first (priority), fallback to nomor_identitas
    // Priority 1: Search by no_pek
    $sql = "SELECT * FROM visits 
            WHERE no_pek = :no_pek 
            AND visit_date = :date 
            AND status = 'MASUK'
            LIMIT 1";
    $visit = getRow($sql, [
        'no_pek' => $noPek,
        'date' => $today
    ]);
    
    // Priority 2: If not found by no_pek, try nomor_identitas
    if (!$visit) {
        $sql = "SELECT * FROM visits 
                WHERE nomor_identitas = :identity 
                AND visit_date = :date 
                AND status = 'MASUK'
                LIMIT 1";
        $visit = getRow($sql, [
            'identity' => $nomorIdentitas,
            'date' => $today
        ]);
    }
    
    if (!$visit) {
        jsonResponse(false, 'Data kunjungan tidak ditemukan. Pastikan NO.PEK atau NO. ID CARD sesuai data saat absen masuk hari ini.');
    }
    
    // Format data for response
    $responseData = [
        'id' => $visit['id'],
        'nama' => $visit['nama'],
        'asal' => $visit['asal'],
        'fungsi' => $visit['fungsi'],
        'jenis_identitas' => $visit['jenis_identitas'],
        'no_pek' => $visit['no_pek'] ?? null,
        'nomor_identitas' => $visit['nomor_identitas'],
        'keperluan' => $visit['keperluan'],
        'jam_masuk' => $visit['jam_masuk'],
        'jam_masuk_formatted' => formatTime($visit['jam_masuk']),
        'keterangan' => $visit['keterangan']
    ];
    
    jsonResponse(true, 'Data kunjungan ditemukan!', $responseData);
    
} catch (Exception $e) {
    error_log('Find Entry Error: ' . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem. Silakan coba lagi.');
}
