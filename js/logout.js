// js/logout.js
// Verarbeitet den Logout-Button.
// Sendet eine GET-Anfrage an logout.php, welche die Server-Session
// beendet. Bei Erfolg wird zur Login-Seite weitergeleitet.

document.getElementById("logoutBtn").addEventListener("click", async (e) => {
  // Prevent the default button behavior
  e.preventDefault();

  try {
    const response = await fetch("api/logout.php", {
      method: "GET",
      credentials: "include",
    });

    const result = await response.json();

    if (result.status === "success") {
      // Redirect to login page after successful logout
      window.location.href = "login.html";
    } else {
      console.error("Logout failed");
      alert("Logout failed. Please try again.");
    }
  } catch (error) {
    console.error("Logout error:", error);
    alert("Something went wrong during logout!");
  }
});
