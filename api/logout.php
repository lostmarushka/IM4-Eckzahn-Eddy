<?php
// api/logout.php
// Beendet die aktive Benutzersession serverseitig.
// Leert das $_SESSION-Array und zerstört die Session vollständig.
// Gibt { "status": "success" } als JSON zurück;
// die Weiterleitung zur Login-Seite übernimmt logout.js.

session_start();
$_SESSION = [];
session_destroy();

// Return a success response instead of redirecting
header('Content-Type: application/json');
echo json_encode(["status" => "success"]);
exit;
?>
