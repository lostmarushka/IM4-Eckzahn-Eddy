<?php
// api/save-event.php
// Speichert ein Putzevent (Zahnputzsession) in der Tabelle 'events'.
// Wird vom Microcontroller oder der WebApp aufgerufen.
// Erwartet einen POST-Request mit JSON-Body:
//   { "kinder_id": <int>, "event": <int>, "dauer": <int> }
// Setzt den Timestamp automatisch auf den aktuellen Zeitpunkt (NOW()).
// Gibt { "success": true } oder eine Fehlermeldung als JSON zurück.

header('Content-Type: application/json');
require_once __DIR__ . '/../system/config.php';

$input = json_decode(file_get_contents('php://input'), true);

$kinder_id = $input['kinder_id'] ?? null;
$event     = $input['event']     ?? null;
$dauer     = $input['dauer']     ?? 0;

if (!$kinder_id || !$event) {
    http_response_code(400);
    echo json_encode(['error' => 'Fehlende Parameter']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO events (kinder_id, timestamp, event, dauer) VALUES (?, NOW(), ?, ?)");
$stmt->execute([$kinder_id, $event, $dauer]);

echo json_encode(['success' => true]);
