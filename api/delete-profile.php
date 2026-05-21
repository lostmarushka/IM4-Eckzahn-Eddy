<?php
require_once 'auth-check.php'; // falls vorhanden, sonst Zeile löschen

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige Methode.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$id   = isset($body['id']) ? (int) $body['id'] : 0;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Ungültige ID.']);
    exit;
}

try {
    require_once 'db.php'; // Datenbankverbindung ($pdo)

    // Zuerst Avatar-Datei löschen, falls vorhanden
    $stmt = $pdo->prepare('SELECT avatar FROM kinder WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Profil nicht gefunden.']);
        exit;
    }

    if (!empty($row['avatar'])) {
        $avatarPath = __DIR__ . '/../' . $row['avatar'];
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }
    }

    // Profil aus DB löschen
    $stmt = $pdo->prepare('DELETE FROM kinder WHERE id = :id');
    $stmt->execute([':id' => $id]);

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Datenbankfehler.']);
}