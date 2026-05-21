<?php
// register.php
session_start();
header('Content-Type: application/json');

require_once '../system/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $email     = trim($data['email']      ?? '');
    $password  = trim($data['password']   ?? '');
    $name      = trim($data['name']       ?? '');
    $familie_id = $data['familie_id']     ?? 1;

    if (!$email || !$password || !$name) {
        echo json_encode(["status" => "error", "message" => "Email, password and name are required"]);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Email is already in use"]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user
    $insert = $pdo->prepare("INSERT INTO users (email, password, name, familie_id) VALUES (:email, :pass, :name, :familie_id)");
    $result = $insert->execute([
        ':email'      => $email,
        ':pass'       => $hashedPassword,
        ':name'       => $name,
        ':familie_id' => $familie_id
    ]);

    if (!$result) {
        echo json_encode(["status" => "error", "message" => $insert->errorInfo()[2]]);
        exit;
    }

    echo json_encode(["status" => "success"]);

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}