<?php
require_once 'auth-check.php'; // falls vorhanden, sonst Zeile löschen

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige Methode.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$id   = isset($body['id'])   ? (int) $body['id']              : 0;
$name = isset($body['name']) ? trim($body['name'])             : '';

if (!$id || $name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige Eingabe.']);
    exit;
}

if (mb_strlen($name) > 30) {
    echo json_encode(['status' => 'error', 'message' => 'Name zu lang (max. 30 Zeichen).']);
    exit;
}

try {
    require_once 'db.php'; // Datenbankverbindung ($pdo)

    $stmt = $pdo->prepare('UPDATE kinder SET name = :name WHERE id = :id');
    $stmt->execute([':name' => $name, ':id' => $id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Profil nicht gefunden.']);
        exit;
    }

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Datenbankfehler.']);
}