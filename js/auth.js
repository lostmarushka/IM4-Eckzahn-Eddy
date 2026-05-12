// Prüft ob der User eingeloggt ist.
// Bei 401 oder Netzwerkfehler → Weiterleitung auf login.html
async function requireAuth() {
  try {
    const response = await fetch("/api/protected.php", {
      credentials: "include",
    });

    if (response.status === 401) {
      window.location.href = "/login.html";
      return null;
    }

    return response.json(); // { email, user_id }
  } catch (error) {
    // Kein Server erreichbar oder anderer Fehler → zur Login-Seite
    console.error("Auth-Check fehlgeschlagen:", error);
    window.location.href = "/login.html";
    return null;
  }
}
