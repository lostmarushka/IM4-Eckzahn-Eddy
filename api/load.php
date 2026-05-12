<?php

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