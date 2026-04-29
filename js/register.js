console.log("Hello World");

document
.getElementById("registerForm")
.addEventListener("submit", async(e) => {
    //hier schreiben wir was beim submit passiert
    e.preventDefault();
    console.log("Form submitted");

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    console.log(email + " " + password);

    try {
        const response = await fetch("/api/register.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded/json" },
            body: JSON.stringify({email, password})
        });

        const result = await response.json();
        console.log(result);
    } catch (error) {
        console.error("Error:", error);
        alert("Error:", error);
    }
});