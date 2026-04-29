<?php

session_start();
header('Content-Type: application/json');

require_once '../system/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//Hier wollen wir die Variablen entpacken

    //Entpacke die Daten
    $data = json_decode(file_get_contents('php://input'), true);
    
   if (isset($data['email'], $data['password'])) {
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    //checken, ob user schon registriert ist

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'User already exists']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); //Password verschlüsseln

    //neue User in die DB einfügen
    $insert = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    $insert->execute([
        ':email' => $email,
        ':password' => $hashedPassword
    ]);

    // an JS zurückschicken
    echo json_encode([
        'status' => 'success', 'email' => $email
        ]);
}

} 

?>