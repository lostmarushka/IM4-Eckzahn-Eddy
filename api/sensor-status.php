<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/../system/config.php'; // Pfad anpassen falls nötig

$stmt = $pdo->query("SELECT id, wert FROM sensordata ORDER BY id DESC LIMIT 1");
$row  = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'id'   => $row ? (int)$row['id'] : null,
    'wert' => $row ? $row['wert']    : null,
]);