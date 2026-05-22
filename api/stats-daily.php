<?php
// api/stats-daily.php
// Liefert die Tagesstatistiken für das aktive Kindprofil.
// Liest die kinder_id aus der Session und fragt alle Events
// des heutigen Tages aus der Tabelle 'events' ab.
//
// Berechnete Werte:
//   sessions      – Anzahl Putzsessions heute
//   completed     – 1 wenn mind. 2 Sessions erfasst wurden, sonst 0
//   minutes       – Gesamtdauer in Minuten (gerundet)
//   goalCompleted – true wenn mind. 3 Sessions heute
//   goalText      – Beschreibung des Tagesziels
//   brushingEvents– Array mit Stunde und normalisierter Dauer
//                   (0–1, Basis: 120 Sek.) je Session

header('Content-Type: application/json');
require_once("../system/config.php");

session_start();
$kinder_id = $_SESSION['kinder_id'] ?? null;
if (!$kinder_id) { echo json_encode(['error' => 'Nicht eingeloggt']); exit; }

$heute = date('Y-m-d');

// Events des aktiven Kindes für heute holen
$stmt = $pdo->prepare("
    SELECT 
        HOUR(timestamp) AS hour,
        dauer
    FROM events
    WHERE kinder_id = ?
      AND DATE(timestamp) = ?
    ORDER BY timestamp ASC
");
$stmt->execute([$kinder_id, $heute]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Metriken berechnen
$sessions  = count($events);
$minutes   = array_sum(array_column($events, 'dauer'));
$completed = ($sessions >= 2) ? 1 : 0;

echo json_encode([
    'sessions'      => $sessions,
    'completed'     => $completed,
    'minutes'       => round($minutes / 60, 1),
    'goalCompleted' => $sessions >= 3,
    'goalText'      => 'Dreimal täglich putzen',
    'brushingEvents'=> array_map(fn($e) => [
        'hour'     => (int)$e['hour'],
        'duration' => (float)($e['dauer'] / 120) // normalisiert auf 0–1
    ], $events)
]);
