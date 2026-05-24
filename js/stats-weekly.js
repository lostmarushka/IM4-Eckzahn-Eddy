// js/stats-weekly.js
// Lädt die Wochenstatistiken für das aktive Kindprofil.
//
// Ablauf:
//   1. Profilname via get-active-profile.php laden und anzeigen.
//   2. Wochendaten via stats-weekly.php abrufen
//      (Sessions, abgeschlossene Tage, Gesamtdauer, Tagesdaten).
//   3. KPI-Werte und Wochenziel-Checkbox ins HTML schreiben.
//   4. Balkendiagramm mit Chart.js erstellen: X-Achse = Wochentage (Mo–So),
//      Y-Achse = Anzahl Putzsessions pro Tag (max. 3).

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

    // 2. Weekly-Daten vom Backend laden
    const res  = await fetch('api/stats-weekly.php');
    const data = await res.json();

    // 3. KPI-Werte setzen
    document.querySelector('.js-stat-sessions').textContent  = data.sessions;
    document.querySelector('.js-stat-completed').textContent = data.completed;
    document.querySelector('.js-stat-minutes').textContent   = data.minutes;
    document.querySelector('.js-goal-text').textContent      = data.goalText;

    const goalCb = document.querySelector('.js-goal-checkbox');
    goalCb.checked = data.goalCompleted;
    goalCb.setAttribute('aria-checked', data.goalCompleted);

    // 4. Bar Chart erstellen
    const canvas = document.querySelector('.js-chart-canvas');
    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'],
            datasets: [{
                label: 'Anzahl',
                data: data.weekData,
                backgroundColor: 'rgba(159, 227, 176, 0.85)',
                borderColor: '#9fe3b0',
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.raw}× geputzt`
                    },
                    backgroundColor: 'rgba(78,31,148,0.9)',
                    titleColor: '#fff',
                    bodyColor: '#9fe3b0',
                    padding: 10,
                    cornerRadius: 10,
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'rgba(255,255,255,0.65)',
                        font: { size: 11, family: 'Inter' }
                    },
                    grid: { color: 'rgba(255,255,255,0.07)' },
                    border: { color: 'transparent' },
                    title: {
                        display: true,
                        text: 'Wochentag',
                        color: 'rgba(255,255,255,0.5)',
                        font: { size: 11, family: 'Inter' }
                    }
                },
                y: {
                    min: 0,
                    max: 3,
                    ticks: {
                        stepSize: 1,
                        color: 'rgba(255,255,255,0.65)',
                        font: { size: 11, family: 'Inter' },
                        callback: val => Number.isInteger(val) ? val : ''
                    },
                    grid: { color: 'rgba(255,255,255,0.07)' },
                    border: { color: 'transparent' },
                    title: {
                        display: true,
                        text: 'Anzahl',
                        color: 'rgba(255,255,255,0.5)',
                        font: { size: 11, family: 'Inter' }
                    }
                }
            }
        }
    });
});
