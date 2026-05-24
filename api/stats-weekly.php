<?php
// api/stats-weekly.php
// Liefert die Wochenstatistiken für das aktive Kindprofil.
// Liest die kinder_id aus der Session und fragt Events
// von Montag bis Sonntag der aktuellen Woche ab.
//
// Enthaltene Daten:
//   sessions      – Gesamtanzahl Sessions diese Woche
//   completed     – Anzahl Tage mit mind. 3 Sessions (Tagesziel erfüllt)
//   minutes       – Gesamtdauer aller Sessions als M:SS
//   goalCompleted – true wenn an allen 7 Tagen das Tagesziel erreicht wurde
//   goalText      – Beschreibung des Wochenziels
//   weekData      – Array [Mo..So] mit Anzahl Sessions pro Tag (max. 3)
//
// MySQL DAYOFWEEK: 1=So, 2=Mo ... 7=Sa → wird auf 0=Mo ... 6=So umgerechnet.

header('Content-Type: application/json');
require_once("../system/config.php");

session_start();
$kinder_id = $_SESSION['kinder_id'] ?? null;
if (!$kinder_id) { echo json_encode(['error' => 'Nicht eingeloggt']); exit; }

// Montag dieser Woche berechnen
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

// Sessions pro Tag
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

// Gesamtdauer der Woche
$stmt2 = $pdo->prepare("
    SELECT SUM(dauer) as totalDauer
    FROM events
    WHERE kinder_id = ?
      AND DATE(timestamp) BETWEEN ? AND ?
");
$stmt2->execute([$kinder_id, $monday, $sunday]);
$dauerRow = $stmt2->fetch(PDO::FETCH_ASSOC);

// Dauer als M:SS formatieren
$totalSek = (int)$dauerRow['totalDauer'];
$minFormatted = floor($totalSek / 60) . ':' . str_pad($totalSek % 60, 2, '0', STR_PAD_LEFT);

echo json_encode([
    'sessions'      => $totalSessions,
    'completed'     => $completedDays,
    'minutes'       => $minFormatted,
    'goalCompleted' => $goalCompleted,
    'goalText'      => 'An 7 Tagen dreimal täglich putzen',
    'weekData'      => $weekData
]);
