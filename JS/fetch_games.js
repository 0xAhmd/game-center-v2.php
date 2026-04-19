// JS/fetch_games.js
// Fetches games + user library IDs (games owned via completed purchases), renders cards.
// The "Add to Library" button has been removed — games are added automatically
// when an admin marks an order as completed.

let _ownedGameIds = new Set();

function fetchGames() {
  const session = window.__session || {};
  const isUser  = session.logged_in && session.role !== 'admin';

  const gamesPromise = fetch('../scripts/get_games.php').then(r => r.json());
  const ownedPromise = isUser
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

        // Show "In Library" badge only — no direct add button
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
        ${isUser && isOwned
          ? `<span class="btn-lib-card owned" style="cursor:default">✓ Owned</span>`
          : ''}
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

function showFetchToast(msg, type = 'success') {
  if (typeof showToast === 'function') {
    showToast(msg, type);
  } else {
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

window.onSessionReady = (function(_orig) {
  return function(s) {
    if (typeof _orig === 'function') _orig(s);
    fetchGames();
  };
})(window.onSessionReady);
