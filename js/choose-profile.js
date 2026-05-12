// choose-profile.js
async function selectProfile(button) {
    const kinderId = button.getAttribute("data-kinder-id");
    const name     = button.getAttribute("data-name");

    try {
        const response = await fetch("api/select-profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ kinder_id: kinderId })
        });
        const result = await response.json();

        if (result.status === "success") {
            // Name für Anzeige in index.html speichern
            sessionStorage.setItem("activeProfile", name);
            window.location.href = "index.html";
        } else {
            alert("Fehler: " + result.message);
        }
    } catch (error) {
        console.error("Fehler beim Profilwechsel:", error);
    }
}