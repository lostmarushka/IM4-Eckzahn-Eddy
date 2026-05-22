<?php
// api/delete-profile.php
// Löscht ein Kindprofil aus der Datenbank.
// Erwartet einen POST-Request mit JSON-Body: { "id": <int> }
// Prüft zuerst, ob das Profil existiert, löscht ggf. die
// zugehörige Avatar-Datei vom Server und entfernt dann den
// Datensatz aus der Tabelle 'kinder'.
// Gibt JSON zurück: { "status": "success" } oder Fehlermeldung.

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
    require_once('../system/config.php');

    // Avatar-Pfad holen
    $stmt = $pdo->prepare('SELECT avatar FROM kinder WHERE id = :id AND familie_id = 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Profil nicht gefunden.']);
        exit;
    }

    // Avatar-Datei löschen falls vorhanden
    if (!empty($row['avatar'])) {
        $avatarPath = __DIR__ . '/../' . $row['avatar'];
        if (file_exists($avatarPath)) {
            unlink($avatarPath);
        }
    }

    // Datensatz löschen
    $stmt = $pdo->prepare('DELETE FROM kinder WHERE id = :id AND familie_id = 1');
    $stmt->execute([':id' => $id]);

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
