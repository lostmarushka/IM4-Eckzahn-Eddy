<?php
// api/sensor-status.php
// Gibt den letzten empfangenen Sensorwert aus der Tabelle 'sensordata' zurück.
// Wird von der WebApp abgefragt, um den aktuellen Status des Sensors
// (z. B. ob der ESP32 Daten sendet) darzustellen.
// Antwort: { "id": <int|null>, "wert": <string|null> }
// Cache-Control: no-store verhindert, dass Browser oder Proxies
// die Antwort zwischenspeichern.

header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/../system/config.php';

$stmt = $pdo->query("SELECT id, wert FROM sensordata ORDER BY id DESC LIMIT 1");
$row  = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'id'   => $row ? (int)$row['id'] : null,
    'wert' => $row ? $row['wert']    : null,
]);
