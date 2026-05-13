<?php
// generate-profile.php
// Verarbeitet das Formular aus generate-profile.html
// und speichert ein neues Kind in der Datenbank.

session_start();
require_once 'db.php';

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

// ── Name validieren ──
$name = trim($_POST['childName'] ?? '');

if ($name === '') {
    header('Location: generate-profile.html?error=name');
    exit;
}

$familien_id = (int) $_SESSION['familien_id'];

// ── In Datenbank speichern ──
try {
    $stmt = $pdo->prepare("
        INSERT INTO kinder (Familien_ID, Name)
        VALUES (:familien_id, :name)
    ");

    $stmt->execute([
        ':familien_id' => $familien_id,
        ':name'        => $name,
    ]);

    header('Location: choose-profile.html?success=1');
    exit;

} catch (PDOException $e) {
    error_log('generate-profile.php DB error: ' . $e->getMessage());
    header('Location: generate-profile.html?error=db');
    exit;
}