<?php
require_once '../config/config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit;
}

$visitId = (int)$_GET['id'];

try {
    $visit = getOne(
        "SELECT tanda_tangan_masuk, tanda_tangan_keluar FROM visits WHERE id = :id",
        ['id' => $visitId]
    );
    
    if ($visit) {
        echo json_encode([
            'success' => true,
            'data' => [
                'tanda_tangan_masuk' => $visit['tanda_tangan_masuk'],
                'tanda_tangan_keluar' => $visit['tanda_tangan_keluar']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
}
