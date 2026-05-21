document.addEventListener('DOMContentLoaded', async () => {

    // 1. Profilname laden (gleich wie in index.html)
    try {
        const profileRes = await fetch('api/get-active-profile.php');
        const profileData = await profileRes.json();
        if (profileData.name) {
            document.querySelector('.js-username').textContent = profileData.name;
        }
    } catch (e) {
        console.warn('Profilname konnte nicht geladen werden:', e);
    }
});