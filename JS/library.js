// JS/library.js — User Game Library logic

/* ════════════════════════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════════════════════════ */
let _allGames = [];     // full library from API
let _sortMode = 'date'; // 'date' | 'title' | 'price'

/* ════════════════════════════════════════════════════════════════
   BOOT: guard + load
═══════════════════════════════════════════════════════════════════ */
fetch('../scripts/session_status.php')
  .then(r => r.json())
  .then(s => {
    if (!s.logged_in) {
      window.location.href = 'login.html';
      return;
    }
    // Admins have no library — redirect to store
    if (s.role === 'admin') {
      window.location.href = 'index.html';
      return;
    }
    loadLibrary();
  })
  .catch(() => { window.location.href = 'login.html'; });

/* ════════════════════════════════════════════════════════════════
   FETCH LIBRARY
═══════════════════════════════════════════════════════════════════ */
function loadLibrary() {
  fetch('../scripts/get_user_library.php')
    .then(r => r.json())
    .then(data => {
      _allGames = data;
      updateCountBadge();
      document.getElementById('libSortBar').style.display =
        data.length ? '' : 'none';
      renderLibrary();
    })
    .catch(() => {
      document.getElementById('libGrid').innerHTML =
        '<div class="col-12"><div class="alert alert-danger">Failed to load library.</div></div>';
    });
}

/* ════════════════════════════════════════════════════════════════
   RENDER
═══════════════════════════════════════════════════════════════════ */
function renderLibrary() {
  const q    = (document.getElementById('libSearchInput')?.value || '').toLowerCase();
  const grid = document.getElementById('libGrid');

  let games = _allGames.filter(g =>
    !q || g.title.toLowerCase().includes(q) || (g.genre || '').toLowerCase().includes(q)
  );

  // Sort
  if (_sortMode === 'title') {
    games = games.sort((a, b) => a.title.localeCompare(b.title));
  } else if (_sortMode === 'price') {
    games = games.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
  }
  // 'date' is default from API (ORDER BY purchase_date DESC)

  if (!games.length && !_allGames.length) {
    grid.innerHTML = `
      <div class="col-12 lib-empty">
        <div class="lib-empty-icon">📭</div>
        <h4 class="text-white">Your library is empty</h4>
        <p class="text-white-50 mt-2">Head to the store and add some games to your collection!</p>
        <a href="index.html" class="btn btn-primary mt-3 px-5">Browse Store</a>
      </div>`;
    return;
  }

  if (!games.length && q) {
    grid.innerHTML = `
      <div class="col-12 text-center py-5">
        <p class="text-white-50">No games match "<strong style="color:#fff">${escHtml(q)}</strong>"</p>
      </div>`;
    return;
  }

  grid.innerHTML = games.map(g => buildCard(g)).join('');
}

function buildCard(g) {
  const img    = g.resolved_image || g.image_url || '../assets/profile.png';
  const price  = parseFloat(g.price) === 0 ? 'Free' : `$${parseFloat(g.price).toFixed(2)}`;
  const date   = new Date(g.purchase_date).toLocaleDateString('en-US',
                   { month: 'short', day: 'numeric', year: 'numeric' });
  const desc   = g.description || 'No description available.';

  return `
<div class="col-md-4 col-sm-6 lib-col" data-game-id="${g.game_id}" data-title="${escAttr(g.title)}">
  <div class="lib-card">
    <span class="owned-ribbon">✓ Owned</span>
    <img src="${escAttr(img)}" alt="${escAttr(g.title)}" class="lib-card-img"
         onerror="this.src='../assets/profile.png'">
    <div class="lib-card-body">
      <div class="lib-card-title" title="${escAttr(g.title)}">${escHtml(g.title)}</div>
      <div class="lib-card-meta">
        <span class="genre-tag">${escHtml(g.genre || 'General')}</span>
        <span class="price-badge" style="font-size:.78rem;padding:3px 10px">${price}</span>
      </div>
      <p class="lib-card-desc">${escHtml(desc)}</p>
      <div class="lib-card-footer">
        <div>
          <div class="lib-purchase-date">📅 Added ${date}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn-play" id="playBtn_${g.game_id}"
                  onclick="handlePlay(${g.game_id}, '${escAttr(g.title)}')">
            ▶ Play
          </button>
          <button class="btn-remove-lib" title="Remove from library"
                  onclick="handleRemove(${g.game_id}, '${escAttr(g.title)}')">✕</button>
        </div>
      </div>
    </div>
  </div>
</div>`;
}

/* ════════════════════════════════════════════════════════════════
   PLAY SIMULATION
═══════════════════════════════════════════════════════════════════ */
function handlePlay(gameId, title) {
  const btn = document.getElementById('playBtn_' + gameId);
  if (!btn || btn.classList.contains('launching')) return;

  btn.classList.add('launching');
  btn.innerHTML = '⏳ Launching…';

  showLibToast('launch', '🚀 Launching Game', `Launching ${title}...`);

  setTimeout(() => {
    btn.classList.remove('launching');
    btn.classList.add('launched');
    btn.innerHTML = '🎮 Playing';

    showLibToast('success', '🎮 Game Launched!', `${title} launched successfully!`);

    // Reset after 4 s
    setTimeout(() => {
      btn.classList.remove('launched');
      btn.innerHTML = '▶ Play';
    }, 4000);
  }, 1500);
}

/* ════════════════════════════════════════════════════════════════
   REMOVE FROM LIBRARY
═══════════════════════════════════════════════════════════════════ */
function handleRemove(gameId, title) {
  if (!confirm(`Remove "${title}" from your library?`)) return;

  const fd = new FormData();
  fd.append('game_id', gameId);

  fetch('../scripts/remove_from_library.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        _allGames = _allGames.filter(g => g.game_id !== gameId);
        updateCountBadge();
        renderLibrary();
        showLibToast('error', '🗑️ Removed', `"${title}" removed from library.`);
        // Hide sort bar if now empty
        document.getElementById('libSortBar').style.display =
          _allGames.length ? '' : 'none';
      } else {
        showLibToast('error', '⚠️ Error', d.error || 'Failed to remove game.');
      }
    })
    .catch(() => showLibToast('error', '⚠️ Error', 'Request failed.'));
}

/* ════════════════════════════════════════════════════════════════
   ADD TO LIBRARY  (called from store page — exported on window)
═══════════════════════════════════════════════════════════════════ */
window.addToLibrary = function(gameId) {
  const fd = new FormData();
  fd.append('game_id', gameId);

  return fetch('../scripts/add_to_library.php', { method: 'POST', body: fd })
    .then(r => r.json());
};

/* ════════════════════════════════════════════════════════════════
   SORT
═══════════════════════════════════════════════════════════════════ */
function setSort(mode) {
  _sortMode = mode;
  document.querySelectorAll('.sort-chip').forEach(c => c.classList.remove('active'));
  event.target.classList.add('active');
  renderLibrary();
}

/* ════════════════════════════════════════════════════════════════
   SEARCH
═══════════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('libSearchInput');
  if (input) {
    input.addEventListener('input', renderLibrary);
  }
});

/* ════════════════════════════════════════════════════════════════
   COUNT BADGE
═══════════════════════════════════════════════════════════════════ */
function updateCountBadge() {
  const badge = document.getElementById('libCountBadge');
  const text  = document.getElementById('libCountText');
  if (!badge || !text) return;
  const n = _allGames.length;
  text.textContent = n === 1 ? '1 game' : `${n} games`;
  badge.style.display = 'inline-flex';

  // Hide loading skeleton
  const loading = document.getElementById('libLoading');
  if (loading) loading.remove();
}

/* ════════════════════════════════════════════════════════════════
   TOAST SYSTEM
═══════════════════════════════════════════════════════════════════ */
function showLibToast(type, title, msg, duration = 3500) {
  const area = document.getElementById('libToastArea');
  if (!area) return;

  const t = document.createElement('div');
  t.className = `lib-toast toast-${type}`;

  const icons = { success: '✅', launch: '🚀', error: '⚠️' };
  t.innerHTML = `
    <div class="lib-toast-icon">${icons[type] || 'ℹ️'}</div>
    <div class="lib-toast-body">
      <div class="lib-toast-title">${escHtml(title)}</div>
      <div class="lib-toast-msg">${escHtml(msg)}</div>
    </div>`;

  area.appendChild(t);
  requestAnimationFrame(() => {
    requestAnimationFrame(() => t.classList.add('visible'));
  });

  setTimeout(() => {
    t.classList.remove('visible');
    setTimeout(() => t.remove(), 320);
  }, duration);
}

/* ════════════════════════════════════════════════════════════════
   UTILS
═══════════════════════════════════════════════════════════════════ */
function escHtml(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function escAttr(s) {
  return String(s ?? '').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
