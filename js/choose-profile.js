// choose-profile.js
// Speichert das gewählte Profil im sessionStorage
// und leitet auf index.html weiter mit dem Namen als URL-Parameter.

function selectProfile(button) {
  const name = button.getAttribute("data-name");

  // Name im sessionStorage speichern (für diese Browser-Session)
  sessionStorage.setItem("activeProfile", name);

  // Weiterleitung auf die Hauptseite
  window.location.href = "index.html?profil=" + encodeURIComponent(name);
}