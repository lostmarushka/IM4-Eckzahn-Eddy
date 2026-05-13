<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true);
$kinder_id = (int) ($data['kinder_id'] ?? 0);

if ($kinder_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige kinder_id']);
    exit;
}

$_SESSION['kinder_id'] = $kinder_id;

echo json_encode(['status' => 'success']);