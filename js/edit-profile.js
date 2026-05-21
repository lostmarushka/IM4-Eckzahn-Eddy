// ── State ────────────────────────────────────────────────────────────
let pendingDeleteId   = null;
let pendingDeleteName = null;

const container   = document.getElementById('profileListContainer');
const alertEl     = document.getElementById('editAlert');
const deleteModal = document.getElementById('delete-modal');
const modalName   = document.getElementById('deleteModalName');
const confirmBtn  = document.getElementById('deleteConfirmBtn');
const cancelBtn   = document.getElementById('deleteCancelBtn');

// ── Helpers ──────────────────────────────────────────────────────────
function showAlert(msg, type = 'error') {
  alertEl.textContent = msg;
  alertEl.className   = `edit-alert edit-alert--${type} is-visible`;
  setTimeout(() => { alertEl.classList.remove('is-visible'); }, 4000);
}

function openDeleteModal(id, name) {
  pendingDeleteId   = id;
  pendingDeleteName = name;
  modalName.textContent = name;
  deleteModal.classList.add('is-open');
  confirmBtn.focus();
}

function closeDeleteModal() {
  deleteModal.classList.remove('is-open');
  pendingDeleteId   = null;
  pendingDeleteName = null;
}

deleteModal.addEventListener('click', (e) => {
  if (e.target === deleteModal) closeDeleteModal();
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && deleteModal.classList.contains('is-open')) {
    closeDeleteModal();
  }
});

cancelBtn.addEventListener('click', closeDeleteModal);

// ── Confirm delete ───────────────────────────────────────────────────
confirmBtn.addEventListener('click', async () => {
  if (!pendingDeleteId) return;

  confirmBtn.disabled    = true;
  confirmBtn.textContent = 'Wird gelöscht…';

  try {
    const res  = await fetch('api/delete-profile.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ id: pendingDeleteId })
    });
    const data = await res.json();

    if (data.status === 'success') {
      const item = document.querySelector(`[data-kinder-id="${pendingDeleteId}"]`);
      if (item) {
        item.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        item.style.opacity    = '0';
        item.style.transform  = 'translateX(20px)';
        setTimeout(() => item.remove(), 260);
      }
      closeDeleteModal();
      showAlert(`Profil "${pendingDeleteName}" wurde gelöscht.`, 'success');
    } else {
      showAlert(data.message || 'Löschen fehlgeschlagen.');
      closeDeleteModal();
    }
  } catch (e) {
    showAlert('Netzwerkfehler. Bitte versuche es erneut.');
    closeDeleteModal();
  } finally {
    confirmBtn.disabled    = false;
    confirmBtn.textContent = 'Ja, löschen';
  }
});

// ── Toggle rename form ───────────────────────────────────────────────
function toggleEdit(item) {
  const nameEl  = item.querySelector('.profile-display-name');
  const form    = item.querySelector('.profile-edit-form');
  const editBtn = item.querySelector('.profile-action-btn--edit');
  const input   = item.querySelector('.profile-name-input');

  const isActive = form.classList.contains('is-active');

  if (isActive) {
    form.classList.remove('is-active');
    nameEl.style.display = '';
    editBtn.classList.remove('is-editing');
  } else {
    form.classList.add('is-active');
    nameEl.style.display = 'none';
    editBtn.classList.add('is-editing');
    input.value = nameEl.textContent;
    input.focus();
    input.select();
  }
}

// ── Save renamed profile ─────────────────────────────────────────────
async function saveRename(item) {
  const nameEl  = item.querySelector('.profile-display-name');
  const form    = item.querySelector('.profile-edit-form');
  const editBtn = item.querySelector('.profile-action-btn--edit');
  const input   = item.querySelector('.profile-name-input');
  const id      = item.dataset.kinderId;
  const newName = input.value.trim();

  if (!newName) { input.focus(); return; }

  if (newName === nameEl.textContent) {
    form.classList.remove('is-active');
    nameEl.style.display = '';
    editBtn.classList.remove('is-editing');
    return;
  }

  try {
    const res  = await fetch('api/rename-profile.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ id, name: newName })
    });
    const data = await res.json();

    if (data.status === 'success') {
      nameEl.textContent   = newName;
      item.dataset.name    = newName;
      form.classList.remove('is-active');
      nameEl.style.display = '';
      editBtn.classList.remove('is-editing');
      showAlert(`Profil wurde in "${newName}" umbenannt.`, 'success');
    } else {
      showAlert(data.message || 'Umbenennen fehlgeschlagen.');
    }
  } catch (e) {
    showAlert('Netzwerkfehler. Bitte versuche es erneut.');
  }
}

// ── Build list item ──────────────────────────────────────────────────
function buildListItem(kind) {
  const li = document.createElement('li');
  li.className = 'profile-list-item';
  li.setAttribute('data-kinder-id', kind.id);
  li.setAttribute('data-name', kind.name);

  const avatarHTML = kind.avatar
    ? `<img src="/${kind.avatar}" alt="${kind.name}" loading="lazy" />`
    : `<span class="profile-list-initial">${kind.name.charAt(0).toUpperCase()}</span>`;

  li.innerHTML = `
    <div class="profile-list-avatar">${avatarHTML}</div>

    <div class="profile-list-info">
      <form class="profile-edit-form" onsubmit="return false;">
        <input
          class="profile-name-input"
          type="text"
          maxlength="30"
          aria-label="Neuer Name für ${kind.name}"
        />
        <button type="submit" class="profile-name-save-btn" aria-label="Name speichern">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2.5"
               stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </button>
      </form>
      <span class="profile-display-name">${kind.name}</span>
    </div>

    <div class="profile-list-actions">
      <button class="profile-action-btn profile-action-btn--edit"
              aria-label="Profil ${kind.name} umbenennen" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
      </button>

      <button class="profile-action-btn profile-action-btn--delete"
              aria-label="Profil ${kind.name} löschen" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
          <path d="M10 11v6M14 11v6"/>
          <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
        </svg>
      </button>
    </div>
  `;

  li.querySelector('.profile-action-btn--edit')
    .addEventListener('click', () => toggleEdit(li));

  li.querySelector('.profile-name-save-btn')
    .addEventListener('click', () => saveRename(li));

  li.querySelector('.profile-name-input')
    .addEventListener('keydown', (e) => {
      if (e.key === 'Enter')  { e.preventDefault(); saveRename(li); }
      if (e.key === 'Escape') { toggleEdit(li); }
    });

  li.querySelector('.profile-action-btn--delete')
    .addEventListener('click', () => openDeleteModal(kind.id, kind.name));

  return li;
}

// ── Load profiles ────────────────────────────────────────────────────
async function loadProfiles() {
  try {
    const res  = await fetch('api/get-profiles.php');
    const data = await res.json();

    container.innerHTML = '';

    if (data.status === 'success' && data.kinder.length > 0) {
      const ul = document.createElement('ul');
      ul.className = 'profile-list';
      ul.setAttribute('role', 'list');
      data.kinder.forEach(kind => ul.appendChild(buildListItem(kind)));
      container.appendChild(ul);
    } else {
      container.innerHTML = `
        <p style="text-align:center; color:var(--color-text-soft); padding: var(--space-lg) 0;">
          Noch keine Profile vorhanden.
        </p>`;
    }
  } catch (e) {
    console.error('Profile laden fehlgeschlagen:', e);
    container.innerHTML = `
      <p style="color:rgba(255,80,80,0.9); padding: var(--space-sm) 0; text-align:center; font-weight:600;">
        Profile konnten nicht geladen werden.
      </p>`;
  }
}

loadProfiles();