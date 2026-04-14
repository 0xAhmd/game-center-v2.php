// JS/scripts.js — Search + Modal logic (role-aware)

let currentEditingGameId = null;
let currentGameData      = {};

// ── Search ────────────────────────────────────────────────────────────────
const searchInput = document.getElementById('searchInput');
if (searchInput) {
  searchInput.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase();
    document.querySelectorAll('#game-cards-container .game-card').forEach(card => {
      const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
      card.style.display = title.includes(q) ? '' : 'none';
    });
  });
}

// ── Called by auth_ui.js once session is loaded ───────────────────────────
function onSessionReady(session) {
  window.__session = session;
}

// ── Open modal (shared by card click) ────────────────────────────────────
function openGameModal(title, id) {
  currentEditingGameId = id;

  // Fetch fresh game data from already-loaded cards
  const cards = document.querySelectorAll('#game-cards-container .game-card');
  cards.forEach(card => {
    if (card.querySelector('.card-title')?.textContent === title) {
      currentGameData = {
        id,
        title,
        genre:       card.querySelector('.genre-tag')?.textContent || '',
        description: card.querySelector('.card-text')?.textContent || '',
        price:       card.querySelector('.price-badge')?.textContent.replace('$','').replace('Free','0') || '0',
        image_url:   card.querySelector('img')?.src || ''
      };
    }
  });

  const session = window.__session || {};
  const isAdmin = session.role === 'admin';
  const isUser  = session.logged_in && !isAdmin;

  // ── Admin: show editable form ─────────────────────────────────────────
  if (isAdmin) {
    document.getElementById('editGameForm').style.display    = '';
    document.getElementById('userViewSection').style.display = 'none';
    document.getElementById('adminModalBtns').style.display  = '';
    document.getElementById('userModalBtns').style.display   = 'none';
    document.getElementById('addToCartBtn').style.display    = 'none';
    document.getElementById('deleteGameBtn').style.display   = '';

    document.getElementById('editGameTitle').value       = currentGameData.title;
    document.getElementById('editGameGenre').value       = currentGameData.genre;
    document.getElementById('editGameDescription').value = currentGameData.description;
    document.getElementById('editGamePrice').value       = currentGameData.price;
    document.getElementById('editGameImageUrl').value    = currentGameData.image_url;
    document.getElementById('editGameModalLabel').textContent = 'Edit Game Details';
  } else {
    // ── User / guest: read-only view ────────────────────────────────────
    document.getElementById('editGameForm').style.display    = 'none';
    document.getElementById('userViewSection').style.display = '';
    document.getElementById('adminModalBtns').style.display  = 'none';
    document.getElementById('userModalBtns').style.display   = '';
    document.getElementById('deleteGameBtn').style.display   = 'none';
    document.getElementById('addToCartBtn').style.display    = isUser ? '' : 'none';

    document.getElementById('viewGameTitle').textContent  = currentGameData.title;
    document.getElementById('viewGameGenre').textContent  = currentGameData.genre;
    document.getElementById('viewGameDesc').textContent   = currentGameData.description;
    const price = parseFloat(currentGameData.price);
    document.getElementById('viewGamePrice').textContent  = price === 0 ? 'Free' : `$${price.toFixed(2)}`;
    document.getElementById('viewGameImage').src          = currentGameData.image_url;
    document.getElementById('editGameModalLabel').textContent = currentGameData.title;
  }

  new bootstrap.Modal(document.getElementById('editGameModal')).show();
}

// Alias for backwards compat
function openEditModal(title, id) { openGameModal(title, id); }

// ── Save edit (admin) ─────────────────────────────────────────────────────
function saveEdit() {
  const data = {
    id:          currentEditingGameId,
    title:       document.getElementById('editGameTitle').value,
    genre:       document.getElementById('editGameGenre').value,
    description: document.getElementById('editGameDescription').value.trim(),
    price:       parseFloat(document.getElementById('editGamePrice').value) || 0,
    image_url:   document.getElementById('editGameImageUrl').value,
  };

  fetch('../scripts/update_game.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data).toString()
  })
    .then(r => r.text())
    .then(() => {
      bootstrap.Modal.getInstance(document.getElementById('editGameModal')).hide();
      fetchGames();
      showToast('Game updated successfully!', 'success');
    })
    .catch(err => console.error('Update error:', err));
}

// ── Delete game (admin) ───────────────────────────────────────────────────
function deleteGame() {
  if (!currentEditingGameId || !confirm('Are you sure you want to delete this game?')) return;
  fetch('../scripts/delete_game.php?id=' + currentEditingGameId)
    .then(r => r.text())
    .then(() => {
      bootstrap.Modal.getInstance(document.getElementById('editGameModal')).hide();
      fetchGames();
      showToast('Game deleted.', 'danger');
    })
    .catch(err => console.error('Delete error:', err));
}

// ── Add to cart (user) ────────────────────────────────────────────────────
function addToCartFromModal() {
  if (!currentEditingGameId) return;
  const formData = new FormData();
  formData.append('action', 'add');
  formData.append('game_id', currentEditingGameId);
  formData.append('quantity', 1);

  fetch('../scripts/cart.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        bootstrap.Modal.getInstance(document.getElementById('editGameModal')).hide();
        showToast('Added to cart! 🛒', 'success');
        // Refresh badge
        fetch('../scripts/cart.php?action=count')
          .then(r => r.json())
          .then(d => {
            const badge = document.getElementById('cartCountBadge');
            const count = parseInt(d.total) || 0;
            if (badge) badge.textContent = count > 0 ? count : '';
          });
      } else {
        showToast(d.error || 'Failed to add to cart.', 'danger');
      }
    })
    .catch(() => showToast('Error adding to cart.', 'danger'));
}

// ── Toast notification ────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px';
    document.body.appendChild(container);
  }
  const t = document.createElement('div');
  t.className = `toast-notification toast-${type}`;
  t.textContent = msg;
  container.appendChild(t);
  setTimeout(() => t.classList.add('toast-visible'), 10);
  setTimeout(() => { t.classList.remove('toast-visible'); setTimeout(() => t.remove(), 400); }, 3000);
}
