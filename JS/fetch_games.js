// JS/fetch_games.js
// Fetches games + user library IDs, renders cards with "Add to Library" + "Owned" badges

let _ownedGameIds = new Set(); // game IDs the current user owns

function fetchGames() {
  const session = window.__session || {};
  const isUser  = session.logged_in && session.role !== 'admin';

  // Fetch games + (if user) their owned IDs in parallel
  const gamesPromise  = fetch('../scripts/get_games.php').then(r => r.json());
  const ownedPromise  = isUser
    ? fetch('../scripts/get_library_ids.php').then(r => r.json()).catch(() => [])
    : Promise.resolve([]);

  Promise.all([gamesPromise, ownedPromise])
    .then(([data, ownedIds]) => {
      _ownedGameIds = new Set(ownedIds.map(Number));

      const container = document.getElementById('game-cards-container');
      if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = `<div class="col-12 text-center text-white py-5">
          <p style="font-size:1.2rem">No games found. Add some!</p></div>`;
        return;
      }
      container.innerHTML = '';
      data.forEach(game => {
        const imgSrc  = game.resolved_image || game.image_url || '../assets/profile.png';
        const price   = parseFloat(game.price) === 0 ? 'Free' : `$${parseFloat(game.price).toFixed(2)}`;
        const isOwned = _ownedGameIds.has(parseInt(game.id));

        // "Add to Library" button — only for logged-in non-admin users
        let libBtn = '';
        if (isUser) {
          if (isOwned) {
            libBtn = `<button class="btn-lib-card owned" disabled title="Already in library">
                        ✓ Owned
                      </button>`;
          } else {
            libBtn = `<button class="btn-lib-card" onclick="storeAddToLibrary(event, ${game.id}, this)"
                              title="Add to Library">
                        📚 Add to Library
                      </button>`;
          }
        }

        const ownedBadge = isOwned
          ? `<span class="owned-store-badge">✓ In Library</span>`
          : '';

        const card = `
<div class="col-md-4 col-sm-6 game-card" data-game-id="${game.id}">
  <div class="card card-glass h-100" onclick="openGameModal('${escAttr(game.title)}', ${game.id})">
    <div style="position:relative">
      <img src="${escAttr(imgSrc)}" class="card-img-top game-img" alt="${escAttr(game.title)}"
           onerror="this.src='../assets/profile.png'">
      ${ownedBadge}
    </div>
    <div class="card-body d-flex flex-column">
      <h5 class="card-title text-white mb-1">${escHtml(game.title)}</h5>
      <span class="genre-tag mb-2">${escHtml(game.genre || 'General')}</span>
      <p class="card-text text-white-50 flex-grow-1" style="font-size:.85rem;line-height:1.4">
        ${escHtml((game.description || '').substring(0, 120))}${game.description && game.description.length > 120 ? '…' : ''}
      </p>
      <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <span class="price-badge">${price}</span>
        ${libBtn}
      </div>
    </div>
  </div>
</div>`;
        container.innerHTML += card;
      });
    })
    .catch(err => {
      console.error('Error fetching games:', err);
      document.getElementById('game-cards-container').innerHTML =
        `<div class="col-12 text-center text-white py-5"><p>Failed to load games.</p></div>`;
    });
}

/* ── Add to Library from store card ────────────────────────────────────── */
function storeAddToLibrary(event, gameId, btn) {
  event.stopPropagation(); // don't open modal
  btn.disabled  = true;
  btn.innerHTML = '⏳ Adding…';

  const fd = new FormData();
  fd.append('game_id', gameId);

  fetch('../scripts/add_to_library.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.success || d.error === 'already_owned') {
        // Mark as owned
        btn.classList.add('owned');
        btn.innerHTML = '✓ Owned';
        btn.disabled  = true;
        btn.title     = 'Already in library';
        _ownedGameIds.add(Number(gameId));

        // Add "In Library" badge to image
        const card = btn.closest('.game-card');
        if (card) {
          const imgWrap = card.querySelector('[style*="position:relative"]');
          if (imgWrap && !imgWrap.querySelector('.owned-store-badge')) {
            const badge = document.createElement('span');
            badge.className   = 'owned-store-badge';
            badge.textContent = '✓ In Library';
            imgWrap.appendChild(badge);
          }
        }

        showFetchToast(d.success
          ? d.message
          : 'Already in your library!', 'success');
      } else {
        btn.disabled  = false;
        btn.innerHTML = '📚 Add to Library';
        showFetchToast(d.error || 'Failed to add to library.', 'danger');
      }
    })
    .catch(() => {
      btn.disabled  = false;
      btn.innerHTML = '📚 Add to Library';
      showFetchToast('Error — please try again.', 'danger');
    });
}

/* ── Reuse the scripts.js toast ─────────────────────────────────────────── */
function showFetchToast(msg, type = 'success') {
  if (typeof showToast === 'function') {
    showToast(msg, type);
  } else {
    // Fallback inline toast
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.style.cssText =
        'position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px';
      document.body.appendChild(container);
    }
    const t = document.createElement('div');
    t.className = `toast-notification toast-${type}`;
    t.textContent = msg;
    container.appendChild(t);
    setTimeout(() => t.classList.add('toast-visible'), 10);
    setTimeout(() => { t.classList.remove('toast-visible'); setTimeout(() => t.remove(), 400); }, 3000);
  }
}

function escHtml(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function escAttr(str) {
  return String(str ?? '').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

// Wait for session to be ready before fetching (auth_ui.js sets window.__session)
// We hook into onSessionReady if defined, otherwise fire on DOMContentLoaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    // If auth_ui hasn't fired yet, wait for it
    if (window.__session !== undefined) {
      fetchGames();
    } else {
      window._pendingFetchGames = true;
    }
  });
} else {
  if (window.__session !== undefined) {
    fetchGames();
  } else {
    window._pendingFetchGames = true;
  }
}

// Called by auth_ui.js indirectly — scripts.js defines onSessionReady,
// but fetch_games also needs the session. We patch via a flag.
const _origOnSessionReady = window.onSessionReady;
window.onSessionReady = function(s) {
  if (_origOnSessionReady) _origOnSessionReady(s);
  if (window._pendingFetchGames) {
    window._pendingFetchGames = false;
    fetchGames();
  }
};
