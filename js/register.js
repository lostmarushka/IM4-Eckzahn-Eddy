// js/register.js
// Verarbeitet das Registrierungsformular.
// Liest Name, E-Mail und Passwort aus, sendet sie als JSON per POST
// an register.php und leitet bei Erfolg zur Login-Seite weiter.
// Bei einem Fehler (z. B. E-Mail bereits vergeben) wird eine
// Fehlermeldung angezeigt.

document
  .getElementById("registerForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const name     = document.getElementById("name").value.trim();
    const email    = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
      const response = await fetch("api/register.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ name, email, password }),
      });
      const result = await response.json();

      console.log("Server response:", result);

      if (result.status === "success") {
  window.location.href = "login.html";
} else {
  alert(result.message || "Registration failed.");
}
} catch (error) {
  console.error("Error:", error);
  alert("Something went wrong!");
}
});
