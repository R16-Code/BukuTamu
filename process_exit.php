<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    // Validate required fields
    $required = ['visit_id', 'tanda_tangan_keluar'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            jsonResponse(false, "Field {$field} wajib diisi!");
        }
    }
    
    $visitId = $_POST['visit_id'];
    $tandaTanganKeluar = $_POST['tanda_tangan_keluar'];
    
    // Validate signature (must be base64 image)
    if (!preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $tandaTanganKeluar)) {
        jsonResponse(false, 'Format tanda tangan tidak valid!');
    }
    
    // Verify visit exists and is in MASUK status
    $visit = getRow("SELECT * FROM visits WHERE id = :id", ['id' => $visitId]);
    
    if (!$visit) {
        jsonResponse(false, 'Data kunjungan tidak ditemukan!');
    }
    
    if ($visit['status'] !== 'MASUK') {
        jsonResponse(false, 'Kunjungan ini sudah selesai (sudah absen keluar).');
    }
    
    // Save exit signature as file
    $signatureDir = __DIR__ . '/uploads/signatures';
    if (!is_dir($signatureDir)) {
        mkdir($signatureDir, 0755, true);
    }
    
    // Generate unique filename
    $signatureFilename = 'keluar_' . date('Ymd_His') . '_' . uniqid() . '.png';
    $signaturePath = $signatureDir . '/' . $signatureFilename;
    
    // Decode and save the base64 image
    $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $tandaTanganKeluar));
    if ($imageData === false || !file_put_contents($signaturePath, $imageData)) {
        jsonResponse(false, 'Gagal menyimpan tanda tangan!');
    }
    
    // Store relative path in database
    $signatureRelativePath = 'uploads/signatures/' . $signatureFilename;
    
    // Update visit with exit information
    $updated = update('visits', 
        [
            'jam_keluar' => date('Y-m-d H:i:s'),
            'tanda_tangan_keluar' => $signatureRelativePath,
            'status' => 'SELESAI',
            'is_flagged' => 0,  // Clear flag if exists
            'flag_note' => null  // Clear flag note
        ],
        'id = :id',
        ['id' => $visitId]
    );
    
    if ($updated > 0) {
        jsonResponse(true, '✅ Terima kasih! Absen keluar berhasil. Sampai jumpa kembali!', [
            'jam_keluar' => formatTime(date('Y-m-d H:i:s'))
        ]);
    } else {
        jsonResponse(false, 'Gagal menyimpan data keluar. Silakan coba lagi.');
    }
    
} catch (Exception $e) {
    error_log('Exit Error: ' . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem. Silakan coba lagi.');
}
