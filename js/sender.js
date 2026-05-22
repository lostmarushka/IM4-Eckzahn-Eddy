// js/sender.js
// Testformular zum manuellen Senden von Sensordaten an die Datenbank.
// Liest den Wert aus dem Formular, formatiert ihn als JSON-String
// und schickt ihn per HTTP POST an load.php.
// Im produktiven Betrieb übernimmt der ESP32 diese Aufgabe;
// dieses Script dient nur zu Testzwecken.

const form = document.getElementById("dataForm");

form.addEventListener("submit", async (event) => {
  event.preventDefault(); // Neuladen der Seite verhindern

  // Daten aus dem Formular holen
  const formData = new FormData(event.target);
  const dataObject = {
    wert: formData.get("wert"),
  };

  // Daten als JSON string formattieren
  const jsonstring = JSON.stringify(dataObject);

  // debug
  console.log("JSON Output:", jsonstring);
  document.querySelector("#message").innerText =
    "Daten gesendet: " + jsonstring;

  // HTTP POST Request an load.php schicken
  try {
    const response = await fetch("api/load.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: jsonstring,
    });
  } catch (error) {
    console.error("Fehler beim Senden der Daten:", error);
    document.querySelector("#message").innerText =
      "Fehler beim Senden der Daten: " + error.message;
  }
});
