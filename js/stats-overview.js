// js/stats-overview.js
// Lädt die Übersichtsstatistiken für das aktive Kindprofil.
//
// Ablauf:
//   1. Profilname via get-active-profile.php laden und anzeigen.
//   2. Übersichtsdaten via stats-overview.php abrufen
//      (Wochenanzahl, Score, letzte 10 Aktivitäten).
//   3. Wochenanzahl und Score ins HTML schreiben.
//   4. Aktivitätsliste rendern: jede Session wird mit Zeitstempel,
//      Dauer und Status (abgeschlossen / abgebrochen) dargestellt.
//      Abgeschlossene Sessions erhalten ein Herz-Icon.

document.addEventListener('DOMContentLoaded', async () => {

    // 1. Profilname laden
    try {
        const profileRes  = await fetch('api/get-active-profile.php');
        const profileData = await profileRes.json();
        if (profileData.name) {
            document.querySelector('.js-username').textContent = profileData.name;
        }
    } catch (e) {
        console.warn('Profilname konnte nicht geladen werden:', e);
    }

    // 2. Übersicht-Daten laden
    const res  = await fetch('api/stats-overview.php');
    const data = await res.json();

    // 3. Woche + Score setzen
    document.querySelector('.js-week-total').textContent = data.weekTotal;
    document.querySelector('.js-score-value').textContent = data.score;

    // 4. Aktivitätsliste rendern
    const list = document.querySelector('.js-activity-list');
    list.innerHTML = '';

    if (!data.activities.length) {
        list.innerHTML = '<li class="stats-activity-empty">Noch keine Aktivitäten</li>';
        return;
    }

    data.activities.forEach(({ label, dauer, completed }) => {
        // Icon: grünes Kreuz = erfolgreich, gelbes = abgebrochen
        const iconSrc = completed ? 'img/cross_green.svg' : 'img/cross_red.svg';
        const statusText = completed
            ? `Dauer: ${dauer}`
            : `Dauer: ${dauer} (abgebrochen)`;

        list.insertAdjacentHTML('beforeend', `
            <li class="stats-activity-item ${completed ? '' : 'stats-activity-item--aborted'}">
                <img src="${iconSrc}" alt="" class="stats-activity-icon" aria-hidden="true" />
                <div class="stats-activity-text">
                    <span class="stats-activity-time">${label}</span>
                    <span class="stats-activity-duration">${statusText}</span>
                </div>
                ${completed ? '<img src="img/heart_pink.svg" alt="Abgeschlossen" class="stats-activity-heart" />' : ''}
            </li>
        `);
    });
});
