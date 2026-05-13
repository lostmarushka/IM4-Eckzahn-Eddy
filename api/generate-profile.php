<?php
// generate-profile.php
// Verarbeitet das Formular aus generate-profile.html
// und speichert ein neues Kind in der Datenbank.

session_start();
require_once '../system/config.php';

// ── Zugriff nur für eingeloggte Benutzer ──
if (empty($_SESSION['familien_id'])) {
    header('Location: index.html');
    exit;
}

// ── Nur POST akzeptieren ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: generate-profile.html');
    exit;
}

// ── Eingaben validieren ──
$name     = trim($_POST['childName'] ?? '');
$brush_id = trim($_POST['brushId']   ?? '');

if ($name === '') {
    // Zurück zum Formular mit Fehlermeldung
    header('Location: generate-profile.html?error=name');
    exit;
}

$familien_id = (int) $_SESSION['familien_id'];

// ── Profilbild hochladen (optional) ──
$avatar_path = null;

if (!empty($_FILES['avatar']['tmp_name'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $file_type     = mime_content_type($_FILES['avatar']['tmp_name']);

    if (in_array($file_type, $allowed_types)) {
        $ext        = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $filename   = 'avatar_' . $familien_id . '_' . time() . '.' . $ext;
        $upload_dir = __DIR__ . '/img/avatars/';

        // Ordner anlegen falls nicht vorhanden
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_dir . $filename)) {
            $avatar_path = 'img/avatars/' . $filename;
        }
    }
}

// ── In Datenbank speichern ──
try {
    $stmt = $pdo->prepare("
        INSERT INTO kinder (Familien_ID, Name, Zahnbuersten_Nummer, Avatar)
        VALUES (:familien_id, :name, :brush_id, :avatar)
    ");

    $stmt->execute([
        ':familien_id' => $familien_id,
        ':name'        => $name,
        ':brush_id'    => $brush_id !== '' ? $brush_id : null,
        ':avatar'      => $avatar_path,
    ]);

    // Erfolgreich → zur Profilauswahl
    header('Location: choose-profile.html?success=1');
    exit;

} catch (PDOException $e) {
    // Im Produktionsbetrieb: Fehler loggen, nicht ausgeben
    error_log('generate-profile.php DB error: ' . $e->getMessage());
    header('Location: generate-profile.html?error=db');
    exit;
}
