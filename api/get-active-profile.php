<?php
// api/get-active-profile.php
// Gibt den Namen und die ID des aktuell aktiven Kindprofils zurück.
// Die kinder_id wird aus der Session gelesen (gesetzt von select-profile.php)
// und der zugehörige Name aus der Tabelle 'kinder' nachgeschlagen.
// Antwort: { "status": "ok", "name": "...", "id": <int> }
// Bei fehlendem Profil in der Session: name und id sind null.
// Bei fehlendem Login: HTTP 401.

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
