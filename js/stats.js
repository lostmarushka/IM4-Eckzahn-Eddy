// ── js/stats.js ──────────────────────────────────────────
// Wird von stats.html UND stats-overview.html eingebunden.
// Seite wird anhand von document.body.dataset.page erkannt.

// ── Data Layer ── später durch fetch() ersetzen ──────────
async function loadStatsData() {
  // TODO: ersetzen mit:
  // const res = await fetch('/api/stats?userId=' + CURRENT_USER.id);
  // return await res.json();
  return {
    streak: 5,
    daily: {
      sessions: 1, completed: 0, minutes: 0,
      goalCompleted: false, goalText: 'Brush twice a day',
      brushingEvents: [{ hour: 12, duration: 0.6 }]
    },
    weekly: {
      sessions: 5, completed: 3, minutes: 9,
      goalCompleted: true, goalText: 'Brush twice a day',
      brushingEvents: [
        { day: 0, duration: 0.3 }, { day: 1, duration: 0.9 },
        { day: 2, duration: 0.6 }, { day: 4, duration: 0.4 },
        { day: 6, duration: 0.3 }
      ]
    }
  };
}

// ── Shared Helpers ────────────────────────────────────────
const HOURS      = ['6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21'];
const DAYS       = ['Mo','Di','Mi','Do','Fr','Sa','So'];
const HOUR_START = 6;

function buildChartData(events, isDaily) {
  if (isDaily) {
    const data = new Array(HOURS.length).fill(0);
    events.forEach(({ hour, duration }) => {
      const idx = hour - HOUR_START;
      if (idx >= 0 && idx < data.length) data[idx] = Math.min(Math.max(Math.round(duration * 3), 1), 3);
    });
    return data;
  } else {
    const data = new Array(DAYS.length).fill(0);
    events.forEach(({ day, duration }) => {
      if (day >= 0 && day < data.length) data[day] = Math.min(Math.max(Math.round(duration * 3), 1), 3);
    });
    return data;
  }
}

// ── Stats Detail Page (stats.html) ───────────────────────
function initDetailPage(data) {
  const elUsername   = document.querySelector('.js-username');
  const elSessions   = document.querySelector('.js-stat-sessions');
  const elCompleted  = document.querySelector('.js-stat-completed');
  const elMinutes    = document.querySelector('.js-stat-minutes');
  const elGoalCb     = document.querySelector('.js-goal-checkbox');
  const elGoalText   = document.querySelector('.js-goal-text');
  const elChartTitle = document.querySelector('.js-chart-title');
  const elOverTitle  = document.querySelector('.js-overview-title');
  const elGoalTitle  = document.querySelector('.js-goal-title');
  const tabDaily     = document.querySelector('.js-tab-daily');
  const tabWeekly    = document.querySelector('.js-tab-weekly');
  const chartCanvas  = document.querySelector('.js-chart-canvas');

  if (elUsername) elUsername.textContent = window.CURRENT_USER?.name ?? '–';

  let chart = null;

  function initChart(events, isDaily) {
    if (chart) chart.destroy();
    chart = new Chart(chartCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: isDaily ? HOURS : DAYS,
        datasets: [{
          data: buildChartData(events, isDaily),
          borderColor: '#9fe3b0',
          backgroundColor: 'rgba(159,227,176,0.12)',
          pointBackgroundColor: '#9fe3b0',
          pointBorderColor: '#9fe3b0',
          pointRadius: 5, pointHoverRadius: 7,
          fill: true, tension: 0.3, borderWidth: 2,
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: { label: ctx => ` ${(ctx.raw * 60).toFixed(0)} Sek.` },
            backgroundColor: 'rgba(78,31,148,0.9)',
            titleColor: '#fff', bodyColor: '#9fe3b0',
            padding: 10, cornerRadius: 10,
          }
        },
        scales: {
          x: {
            ticks: { color: 'rgba(255,255,255,0.65)', font: { size: 11, family: 'Inter' }, maxRotation: 0 },
            grid: { color: 'rgba(255,255,255,0.07)' }, border: { color: 'transparent' }
          },
          y: {
            min: 0, max: 3,
            ticks: { color: 'rgba(255,255,255,0.65)', font: { size: 11, family: 'Inter' }, stepSize: 1, callback: val => Number.isInteger(val) ? val : '' },
            grid: { color: 'rgba(255,255,255,0.07)' }, border: { color: 'transparent' }
          }
        }
      }
    });
  }

  function renderStats(d, isDaily) {
    elSessions.textContent   = d.sessions;
    elCompleted.textContent  = d.completed;
    elMinutes.textContent    = d.minutes;
    elGoalCb.checked         = d.goalCompleted;
    elGoalCb.setAttribute('aria-checked', d.goalCompleted);
    elGoalText.textContent   = d.goalText;
    elChartTitle.textContent = isDaily ? 'Brushing Times Today'     : 'Brushing Times This Week';
    elOverTitle.textContent  = isDaily ? "Today's Overview"         : "This Week's Overview";
    elGoalTitle.textContent  = isDaily ? 'Daily Goal'               : 'Weekly Goal';
    initChart(d.brushingEvents, isDaily);
  }

  function switchTab(isDaily) {
    tabDaily.classList.toggle('stats-tab--active', isDaily);
    tabWeekly.classList.toggle('stats-tab--active', !isDaily);
    tabDaily.setAttribute('aria-selected', isDaily);
    tabWeekly.setAttribute('aria-selected', !isDaily);
    renderStats(isDaily ? data.daily : data.weekly, isDaily);
  }

  tabDaily.addEventListener('click',  () => switchTab(true));
  tabWeekly.addEventListener('click', () => switchTab(false));

  // Tab aus URL-Parameter lesen (?tab=weekly)
  const urlTab = new URLSearchParams(location.search).get('tab');
  switchTab(urlTab !== 'weekly');
}

// ── Stats Overview Page (stats-overview.html) ────────────
function initOverviewPage(data) {
  const el = sel => document.querySelector(sel);

  if (el('.js-username')) el('.js-username').textContent = window.CURRENT_USER?.name ?? '–';

  el('.js-streak-count').textContent = data.streak;
  el('.js-streak-sub').textContent   = data.streak >= 7 ? '🏆 Wochenziel erreicht!' : 'Weiter so!';

  const d = data.daily;
  el('.js-daily-sessions').textContent  = d.sessions;
  el('.js-daily-completed').textContent = d.completed;
  el('.js-daily-minutes').textContent   = d.minutes;
  el('.js-daily-goal-text').textContent = d.goalText;
  el('.js-daily-goal-dot').classList.toggle('stats-overview-goal-dot--done', d.goalCompleted);

  const w = data.weekly;
  el('.js-weekly-sessions').textContent  = w.sessions;
  el('.js-weekly-completed').textContent = w.completed;
  el('.js-weekly-minutes').textContent   = w.minutes;
  el('.js-weekly-goal-text').textContent = w.goalText;
  el('.js-weekly-goal-dot').classList.toggle('stats-overview-goal-dot--done', w.goalCompleted);

  document.querySelectorAll('.stats-overview-bar--weekly').forEach(bar => {
    const day   = parseInt(bar.dataset.day);
    const event = w.brushingEvents.find(e => e.day === day);
    const pct   = event ? Math.round((event.duration / 1) * 100) : 0;
    bar.style.height = pct + '%';
    if (event) bar.classList.add('stats-overview-bar--active');
  });
}

// ── Entry Point ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  const data = await loadStatsData();
  const page = document.body.dataset.page;

  if (page === 'stats-detail')   initDetailPage(data);
  if (page === 'stats-overview') initOverviewPage(data);
});