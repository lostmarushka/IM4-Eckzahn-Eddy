// js/stats-daily.js
document.addEventListener('DOMContentLoaded', async () => {

    // 1. Profilname laden
    try {
        const profileRes = await fetch('api/get-active-profile.php');
        const profileData = await profileRes.json();
        if (profileData.name) {
            document.querySelector('.js-username').textContent = profileData.name;
        }
    } catch (e) {
        console.warn('Profilname konnte nicht geladen werden:', e);
    }

    // 2. Echte Daten vom Backend laden
    const res  = await fetch('api/stats-daily.php');
    const data = await res.json();

    // 3. KPI-Werte in die HTML-Elemente schreiben
    document.querySelector('.js-stat-sessions').textContent  = data.sessions;
    document.querySelector('.js-stat-completed').textContent = data.completed;
    document.querySelector('.js-stat-minutes').textContent   = data.minutes;
    document.querySelector('.js-goal-text').textContent      = data.goalText;

    // 4. Chart bauen
    const HOURS     = ['0','1','2','3','4','5','6','7','8','9','10','11',
                       '12','13','14','15','16','17','18','19','20','21','22','23'];
    const chartData = new Array(24).fill(0);

    data.brushingEvents.forEach(({ hour }) => {
        if (hour >= 0 && hour < 24) chartData[hour] = 1;
    });

    const canvas = document.querySelector('.js-chart-canvas');
    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: HOURS,
            datasets: [{
                data: chartData,
                borderColor: '#9fe3b0',
                backgroundColor: 'rgba(159,227,176,0.12)',
                pointBackgroundColor: '#9fe3b0',
                pointRadius: 5,
                fill: true,
                tension: 0.3,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    ticks: { color: 'rgba(255,255,255,0.65)', font: { size: 11 } },
                    grid:  { color: 'rgba(255,255,255,0.07)' },
                    border: { color: 'transparent' },
                    title: {
                        display: true,
                        text: 'Tageszeit',
                        color: 'rgba(255,255,255,0.5)',
                        font: { size: 11, family: 'Inter' }
                    }
                },
                y: {
                    min: 0, max: 3,
                    ticks: {
                        stepSize: 1,
                        color: 'rgba(255,255,255,0.65)',
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