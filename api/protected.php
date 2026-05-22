<?php
// api/protected.php
// Geschützter API-Endpunkt: prüft, ob eine gültige Benutzersession
// vorhanden ist. Wird von auth.js und protected.js beim Seitenaufruf
// abgefragt, um den Zugriff auf gesicherte Seiten zu steuern.
// Bei gültiger Session: JSON mit user_id und E-Mail.
// Bei fehlender Session: HTTP 401 + JSON-Fehlermeldung.

session_start();

if (!isset($_SESSION['user_id'])) {
    // Instead of redirect, return a 401 JSON response
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// If they are logged in, return user data
echo json_encode([
    "status" => "success",
    "user_id" => $_SESSION['user_id'],
    "email" => $_SESSION['email']
]);
