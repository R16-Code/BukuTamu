<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

try {
    // Validate required fields
    $required = ['tanggal', 'nama', 'asal', 'fungsi', 'jenis_identitas', 'nomor_identitas', 'no_pek', 'keperluan', 'tanda_tangan'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            jsonResponse(false, "Field {$field} wajib diisi!");
        }
    }
    
    // Get form data
    $tanggal = $_POST['tanggal'];
    $nama = trim($_POST['nama']);
    $asal = trim($_POST['asal']);
    $fungsi = trim($_POST['fungsi']);
    $jenisIdentitas = $_POST['jenis_identitas'];
    $nomorIdentitas = trim($_POST['nomor_identitas']);
    $noPek = isset($_POST['no_pek']) ? trim($_POST['no_pek']) : null;
    $keperluan = trim($_POST['keperluan']);
    $tandaTangan = $_POST['tanda_tangan'];
    $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : null;
    
    // Handle "Lainnya" identity type
    if ($jenisIdentitas === 'LAINNYA') {
        if (empty($_POST['jenis_identitas_lainnya'])) {
            jsonResponse(false, 'Silakan isi jenis identitas yang digunakan!');
        }
        $jenisIdentitas = trim($_POST['jenis_identitas_lainnya']);
    }
    
    // Validate jenis identitas
    $validIdentitas = ['KTP', 'KTM', 'SIM', 'PASPORT', 'ID_CARD'];
    if (!in_array($jenisIdentitas, $validIdentitas) && empty($_POST['jenis_identitas_lainnya'])) {
        // If not in valid list and not custom, reject
        if (!in_array($_POST['jenis_identitas'], array_merge($validIdentitas, ['LAINNYA']))) {
            jsonResponse(false, 'Jenis identitas tidak valid!');
        }
    }
    
    // Validate signature (must be base64 image)
    if (!preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $tandaTangan)) {
        jsonResponse(false, 'Format tanda tangan tidak valid!');
    }
    
    // Check duplicate entry (same no_pek, same date, status MASUK)
    $duplicate = checkDuplicateEntry($noPek, $tanggal);
    if ($duplicate) {
        jsonResponse(false, 'NO.PEK/NIK/SIM/PASPORT tersebut masih digunakan dan belum melakukan absen keluar. Silakan lakukan absen keluar terlebih dahulu sebelum menggunakan kembali.');
    }
    
    // Check duplicate ID Card (same nomor_identitas, same date, status MASUK)
    $duplicateIdCard = checkDuplicateIdCard($nomorIdentitas, $tanggal);
    if ($duplicateIdCard) {
        jsonResponse(false, 'NO. ID CARD tersebut masih digunakan dan belum melakukan absen keluar. Silakan gunakan NO. ID CARD lain atau lakukan absen keluar terlebih dahulu.');
    }
    
    // Save signature as file
    $signatureDir = __DIR__ . '/uploads/signatures';
    if (!is_dir($signatureDir)) {
        mkdir($signatureDir, 0755, true);
    }
    
    // Generate unique filename
    $signatureFilename = 'masuk_' . date('Ymd_His') . '_' . uniqid() . '.png';
    $signaturePath = $signatureDir . '/' . $signatureFilename;
    
    // Decode and save the base64 image
    $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $tandaTangan));
    if ($imageData === false || !file_put_contents($signaturePath, $imageData)) {
        jsonResponse(false, 'Gagal menyimpan tanda tangan!');
    }
    
    // Store relative path in database
    $signatureRelativePath = 'uploads/signatures/' . $signatureFilename;
    
    // Insert visitor entry
    $visitData = [
        'nomor_identitas' => $nomorIdentitas,
        'no_pek' => $noPek,
        'visit_date' => $tanggal,
        'nama' => $nama,
        'asal' => $asal,
        'fungsi' => $fungsi,
        'jenis_identitas' => $jenisIdentitas,
        'keperluan' => $keperluan,
        'jam_masuk' => date('Y-m-d H:i:s'),
        'tanda_tangan_masuk' => $signatureRelativePath,
        'keterangan' => $keterangan,
        'status' => 'MASUK'
    ];
    
    $visitId = insert('visits', $visitData);
    
    if ($visitId) {
        jsonResponse(true, 'Absen masuk berhasil!✅ Selamat datang, ' . $nama . '!', [
            'visit_id' => $visitId,
            'jam_masuk' => formatDateTime($visitData['jam_masuk'])
        ]);
    } else {
        jsonResponse(false, 'Gagal menyimpan data. Silakan coba lagi.');
    }
    
} catch (Exception $e) {
    error_log('Entry Error: ' . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem: ' . $e->getMessage());
}