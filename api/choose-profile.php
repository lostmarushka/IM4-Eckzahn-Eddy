<?php
// select-profile.php
// Wird aufgerufen wenn ein Kind sein Profil auswählt.
// Speichert die kinder_id in der Session.

session_start();
header('Content-Type: application/json');

require_once '../system/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $kinder_id = intval($data['kinder_id'] ?? 0);

    if (!$kinder_id) {
        echo json_encode(["status" => "error", "message" => "Keine kinder_id übergeben"]);
        exit;
    }

    // Sicherheitscheck: Gehört dieses Kind zur Familie des eingeloggten Users?
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Nicht eingeloggt"]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT k.id FROM kinder k
        JOIN users u ON u.familie_id = k.familie_id
        WHERE k.id = :kinder_id AND u.id = :user_id
    ");
    $stmt->execute([
        ':kinder_id' => $kinder_id,
        ':user_id'   => $_SESSION['user_id']
    ]);
    $kind = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$kind) {
        echo json_encode(["status" => "error", "message" => "Kind nicht gefunden"]);
        exit;
    }

    // Kinder-ID in Session speichern
    $_SESSION['kinder_id'] = $kinder_id;

    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Ungültige Anfrage"]);
}