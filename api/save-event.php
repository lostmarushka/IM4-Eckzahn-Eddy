<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

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