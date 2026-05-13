<?php
session_start();
require_once '../system/config.php';

// Zugriff nur für eingeloggte Benutzer
if (empty($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

// Nur POST akzeptieren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /generate-profile.html');
    exit;
}

// Name validieren
$name = trim($_POST['childName'] ?? '');
if ($name === '') {
    header('Location: /generate-profile.html?error=name');
    exit;
}

// Family-ID aus der Session holen – vorerst fest auf 1 setzen
$familie_id = 1;   // <─ HIER

// In Datenbank speichern
try {
    $stmt = $pdo->prepare("
        INSERT INTO kinder (familie_id, name)
        VALUES (:familie_id, :name)
    ");

    $stmt->execute([
        ':familie_id' => $familie_id,
        ':name'       => $name,
    ]);

    header('Location: /generate-profile.html?success=1');
    exit;

} catch (PDOException $e) {
    error_log('generate-profile.php DB error: ' . $e->getMessage());
    header('Location: /generate-profile.html?error=db');
    exit;
}