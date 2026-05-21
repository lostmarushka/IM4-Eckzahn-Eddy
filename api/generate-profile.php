<?php
// api/generate-profile.php
session_start();
require_once '../system/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /generate-profile.html');
    exit;
}

$name = trim($_POST['childName'] ?? '');
if ($name === '') {
    header('Location: /generate-profile.html?error=name');
    exit;
}

// Familie vorerst fest auf 1
$familie_id = 1;

// Zufälliges Avatar-Bild aus dem img-Ordner
$avatars = [
    'img/profile_emma.jpg',
    'img/profile_sophie.jpg',
    'img/profile_max.jpg',
    'img/profile_zufaellig.jpg',
];

$avatar = $avatars[array_rand($avatars)];

try {
    $stmt = $pdo->prepare(
        'INSERT INTO kinder (familie_id, name, avatar)
         VALUES (:familie_id, :name, :avatar)'
    );
    $stmt->execute([
        ':familie_id' => $familie_id,
        ':name'       => $name,
        ':avatar'     => $avatar,
    ]);

    header('Location: /generate-profile.html?success=1');
    exit;

} catch (PDOException $e) {
    error_log('generate-profile.php DB error: ' . $e->getMessage());
    header('Location: /generate-profile.html?error=db');
    exit;
}
