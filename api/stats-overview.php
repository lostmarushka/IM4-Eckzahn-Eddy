<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../system/config.php';

session_start();
$kinder_id = $_SESSION['kinder_id'] ?? null;
if (!$kinder_id) { echo json_encode(['error' => 'Nicht eingeloggt']); exit; }

// Letzte 10 Aktivitäten
$stmt = $pdo->prepare("
    SELECT timestamp, dauer
    FROM events
    WHERE kinder_id = ?
    ORDER BY timestamp DESC
    LIMIT 10
");
$stmt->execute([$kinder_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$activities = [];
foreach ($rows as $row) {
    $ts        = strtotime($row['timestamp']);
    $today     = date('Y-m-d');
    $rowDate   = date('Y-m-d', $ts);
    $completed = (int)$row['dauer'] >= 120;

    // Datum formatieren: "Heute" oder "28. Apr."
    if ($rowDate === $today) {
        $label = 'Heute, ' . date('H:i', $ts) . ' Uhr';
    } else {
        $months = ['','Jan.','Feb.','März','Apr.','Mai','Jun.','Jul.','Aug.','Sep.','Okt.','Nov.','Dez.'];
        $label  = date('j', $ts) . '. ' . $months[(int)date('n', $ts)] . ', ' . date('G:i', $ts) . ' Uhr';
    }

    // Dauer formatieren: Sekunden → M:SS
    $sec   = (int)$row['dauer'];
    $dauer = floor($sec / 60) . ':' . str_pad($sec % 60, 2, '0', STR_PAD_LEFT);

    $activities[] = [
        'label'     => $label,
        'dauer'     => $dauer,
        'completed' => $completed,
    ];
}

// Diese-Woche Stats
$monday = date('Y-m-d', strtotime('monday this week'));
$stmt2  = $pdo->prepare("
    SELECT COUNT(*) as total, SUM(dauer >= 120) as completed
    FROM events
    WHERE kinder_id = ? AND DATE(timestamp) >= ?
");
$stmt2->execute([$kinder_id, $monday]);
$week = $stmt2->fetch(PDO::FETCH_ASSOC);

// Score: abgeschlossene Sessions / Wochenziel (3x täglich × 7 Tage = 21)
$completedSessions = (int)$week['completed'];
$ziel = 21;
$score = min(100, (int)round(($completedSessions / $ziel) * 100));

echo json_encode([
    'activities' => $activities,
    'weekTotal'  => (int)$week['total'],
    'score'      => $score,
]);
