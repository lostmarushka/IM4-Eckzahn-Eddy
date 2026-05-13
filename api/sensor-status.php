<?php
// api/sensor-status.php
// Gibt den neuesten Sensorwert für das aktive Kindprofil zurück
session_start();
require_once '../system/config.php';
header('Content-Type: application/json');

// kinder_id aus der Session (wird von select-profile.php gesetzt)
$kinder_id = (int) ($_SESSION['kinder_id'] ?? 0);

if ($kinder_id <= 0) {
    echo json_encode(['status' => 'ok', 'id' => null, 'wert' => null]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'SELECT id, wert FROM sensordata WHERE kinder_id = :kinder_id ORDER BY id DESC LIMIT 1'
    );
    $stmt->execute([':kinder_id' => $kinder_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode(['status' => 'ok', 'id' => (int)$row['id'], 'wert' => $row['wert']]);
    } else {
        echo json_encode(['status' => 'ok', 'id' => null, 'wert' => null]);
    }
} catch (PDOException $e) {
    error_log('sensor-status.php error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error']);
}