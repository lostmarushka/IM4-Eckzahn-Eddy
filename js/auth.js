// js/auth.js
// Prüft beim Seitenaufruf, ob der Benutzer eingeloggt ist.
// Sendet eine Anfrage an protected.php – bei 401 (nicht autorisiert)
// wird der Benutzer automatisch zur Login-Seite weitergeleitet.
// Das zurückgegebene Promise (authReady) kann von anderen Scripts
// abgewartet werden, um die Benutzerdaten zu erhalten.

const authReady = (async function () {
  const response = await fetch("/api/protected.php", {
    credentials: "include",
  });

  if (response.status === 401) {
    window.location.href = "/login.html";
    return null;
  }

  return response.json();
})();
