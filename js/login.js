// js/login.js
// Verarbeitet das Login-Formular.
// Liest E-Mail und Passwort aus, sendet sie als JSON per POST
// an login.php und leitet bei Erfolg zu choose-profile.html weiter.
// Bei einem Fehler wird eine Fehlermeldung angezeigt.

document.getElementById("loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  try {
    const response = await fetch("api/login.php", {
      method: "POST",
      // credentials: 'include', // uncomment if front-end & back-end are on different domains
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    });
    const result = await response.json();

    if (result.status === "success") {
      alert("Login successful!"); //Kann später gelöscht werde, nur zum testen da
      window.location.href = "choose-profile.html";
    } else {
      alert(result.message || "Login failed.");
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Something went wrong!");
  }
});
