<?php
// api/load.php
// Empfängt Sensordaten vom ESP32 (oder vom Testformular sender.html)
// per HTTP POST als JSON-Body: { "wert": <float>, "kinder_id": <int> }
// Validiert die Eingabe, prüft auf Vollständigkeit und speichert
// den Messwert zusammen mit der kinder_id in der Tabelle 'sensordata'.
// Gibt "OK" bei Erfolg zurück, oder eine Fehlermeldung.

require_once("../system/config.php");

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

// ---------------- VALIDATION ----------------

if (!isset($input["wert"]) || !isset($input["kinder_id"])) {
    echo "Missing data (wert or kinder_id)";
    exit;
}

$wert = $input["wert"];
$kinder_id = $input["kinder_id"];

// optional: einfache Typ-Sicherheit
$wert = floatval($wert);
$kinder_id = intval($kinder_id);

// ---------------- DB INSERT ----------------

$sql = "INSERT INTO sensordata (wert, kinder_id) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$wert, $kinder_id]);

echo "OK";
?>
