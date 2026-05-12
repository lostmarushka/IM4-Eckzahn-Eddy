// choose-profile.js
// Profil auswählen → kinder_id in Session speichern → weiter zu index.html

async function selectProfile(button) {
  const kinderId = button.getAttribute("data-kinder-id");
  const name     = button.getAttribute("data-name");

  // Name sofort im sessionStorage speichern (für Anzeige auf index.html)
  sessionStorage.setItem("activeProfile", name);

  // Versuche kinder_id serverseitig in Session zu speichern
  try {
    const response = await fetch("api/select-profile.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ kinder_id: kinderId })
    });
    const result = await response.json();

    if (result.status === "success") {
      // Alles gut → weiter zu index.html
      window.location.href = "index.html";
    } else {
      // API-Fehler → trotzdem weiterleiten (Name ist bereits gespeichert)
      console.warn("select-profile Fehler:", result.message);
      window.location.href = "index.html";
    }
  } catch (error) {
    // Netzwerkfehler → trotzdem weiterleiten
    console.error("Fehler beim Profilwechsel:", error);
    window.location.href = "index.html";
  }
}