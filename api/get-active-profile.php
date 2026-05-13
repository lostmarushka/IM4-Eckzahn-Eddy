<?php
// api/get-active-profile.php
// Gibt den Namen des aktiven Kindprofils aus der Session zurück
session_start();
require_once '../system/config.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// kinder_id muss in der Session stehen (wird von select-profile.php gesetzt)
if (empty($_SESSION['kinder_id'])) {
    echo json_encode(['status' => 'ok', 'name' => null, 'id' => null]);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT name FROM kinder WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => (int) $_SESSION['kinder_id']]);
    $kind = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'ok',
        'name'   => $kind['name'] ?? null,
        'id'     => (int) $_SESSION['kinder_id']
    ]);
} catch (PDOException $e) {
    error_log('get-active-profile.php DB error: ' . $e->getMessage());
    echo json_encode(['status' => 'ok', 'name' => null, 'id' => null]);
}