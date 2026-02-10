<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    // Validate required fields
    if (empty($_POST['nomor_identitas'])) {
        jsonResponse(false, 'Nomor identitas wajib diisi!');
    }
    
    $nomorIdentitas = trim($_POST['nomor_identitas']);
    $today = date('Y-m-d');
    
    // Find active entry for today
    $sql = "SELECT * FROM visits 
            WHERE nomor_identitas = :identity 
            AND visit_date = :date 
            AND status = 'MASUK'
            LIMIT 1";
    
    $visit = getRow($sql, [
        'identity' => $nomorIdentitas,
        'date' => $today
    ]);
    
    if (!$visit) {
        jsonResponse(false, 'Data kunjungan tidak ditemukan. Pastikan Anda sudah absen masuk hari ini dan menggunakan nomor identitas yang sama.');
    }
    
    // Format data for response
    $responseData = [
        'id' => $visit['id'],
        'nama' => $visit['nama'],
        'asal' => $visit['asal'],
        'fungsi' => $visit['fungsi'],
        'jenis_identitas' => $visit['jenis_identitas'],
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
