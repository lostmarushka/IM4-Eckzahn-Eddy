<?php
// api/get-profiles.php
// Gibt alle Kindprofile der eingeloggten Familie als JSON zurück.
// Liest die ID, den Namen und den Avatar-Pfad aller Einträge
// in der Tabelle 'kinder' für die aktuelle familie_id.
// Antwort: { "status": "success", "kinder": [ { id, name, avatar }, ... ] }
// Bei fehlendem Login: HTTP 401.

session_start();
require_once '../system/config.php';
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Vorerst famille_id = 1
$familie_id = 1;

try {
    $stmt = $pdo->prepare(
        'SELECT id, name, avatar FROM kinder WHERE familie_id = :familie_id ORDER BY id ASC'
    );
    $stmt->execute([':familie_id' => $familie_id]);
    $kinder = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'kinder' => $kinder]);
} catch (PDOException $e) {
    error_log('get-profiles.php DB error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
}
