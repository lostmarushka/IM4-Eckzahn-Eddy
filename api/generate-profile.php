<?php
// api/generate-profile.php
// Nimmt das Formular aus generate-profile.html entgegen
// und speichert ein neues Kind in der Tabelle `kinder`.

session_start();
require_once '../system/config.php'; // DB-Verbindung

// ── Zugriff nur für eingeloggte Benutzer ──
if (empty($_SESSION['user_id'])) {
    // Falls nicht eingeloggt, zurück zur Login-Seite
    header('Location: /login.html');
    exit;
}

// Family-ID aus der Session holen
// (falls du sie an anderer Stelle setzt: $_SESSION['familien_id'])
$familien_id = $_SESSION['familien_id'] ?? $_SESSION['user_id'] ?? null;
if ($familien_id === null) {
    // Fallback: lieber sauber abbrechen als mit NULL in die DB schreiben
    header('Location: /generate-profile.html?error=db');
    exit;
}

// ── Nur POST akzeptieren ──
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /generate-profile.html');
    exit;
}

// ── Name validieren ──
$name = trim($_POST['childName'] ?? '');

if ($name === '') {
    // Zurück mit Fehlermeldung, wird im HTML angezeigt
    header('Location: /generate-profile.html?error=name');
    exit;
}

// ── In Datenbank speichern ──
// WICHTIG: Spaltennamen wie in deiner Tabelle: ID, familie_id, name
try {
    $stmt = $pdo->prepare(
        'INSERT INTO kinder (familie_id, name) 
         VALUES (:familie_id, :name)'
    );

    $stmt->execute([
        ':familie_id' => $familien_id,
        ':name'       => $name,
    ]);

    // Erfolgreich → Erfolgsmeldung auf generate-profile.html
    header('Location: /generate-profile.html?success=1');
    exit;

} catch (PDOException $e) {
    error_log('generate-profile.php DB error: ' . $e->getMessage());
    header('Location: /generate-profile.html?error=db');
    exit;
}