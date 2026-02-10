<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin authentication
requireAdmin();

// Set response header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get visit_id
$visitId = $_POST['visit_id'] ?? null;

if (!$visitId) {
    echo json_encode(['success' => false, 'message' => 'ID data tidak valid.']);
    exit;
}

try {
    // Delete query
    $query = "DELETE FROM visits WHERE id = :id";
    $result = executeQuery($query, ['id' => $visitId]);

    if ($result->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Data kunjungan berhasil dihapus.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan atau sudah dihapus.']);
    }
} catch (Exception $e) {
    error_log("Delete Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menghapus data.']);
}
