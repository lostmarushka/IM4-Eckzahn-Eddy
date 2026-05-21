<?php
header('Content-Type: application/json');
require_once("../system/config.php");

session_start();
$kinder_id = $_SESSION['kinder_id'] ?? null;
if (!$kinder_id) { echo json_encode(['error' => 'Nicht eingeloggt']); exit; }

// Montag dieser Woche berechnen
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

$stmt = $pdo->prepare("
    SELECT 
        DAYOFWEEK(timestamp) AS dow,
        COUNT(*) AS anzahl
    FROM events
    WHERE kinder_id = ?
      AND DATE(timestamp) BETWEEN ? AND ?
    GROUP BY DAYOFWEEK(timestamp)
");
$stmt->execute([$kinder_id, $monday, $sunday]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// MySQL: 1=So, 2=Mo, 3=Di, 4=Mi, 5=Do, 6=Fr, 7=Sa
// Umrechnen auf 0=Mo ... 6=So
$dayMap = [2=>0, 3=>1, 4=>2, 5=>3, 6=>4, 7=>5, 1=>6];
$weekData = array_fill(0, 7, 0);
foreach ($rows as $row) {
    $idx = $dayMap[$row['dow']] ?? null;
    if ($idx !== null) $weekData[$idx] = min((int)$row['anzahl'], 3);
}

// Gesamtmetriken
$totalSessions = array_sum($weekData);
$completedDays = count(array_filter($weekData, fn($v) => $v >= 3));
$goalCompleted  = $completedDays >= 7;

echo json_encode([
    'sessions'    => $totalSessions,
    'completed'   => $completedDays,
    'minutes'     => 0,
    'goalCompleted' => $goalCompleted,
    'goalText'    => 'An 7 Tagen dreimal täglich putzen',
    'weekData'    => $weekData  // [Mo, Di, Mi, Do, Fr, Sa, So]
]);