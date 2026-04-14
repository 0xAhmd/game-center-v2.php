// JS/fetch_games.js
function fetchGames() {
  fetch('../scripts/get_games.php')
    .then(r => r.json())
    .then(data => {
      const container = document.getElementById('game-cards-container');
      if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = `<div class="col-12 text-center text-white py-5">
          <p style="font-size:1.2rem">No games found. Add some!</p></div>`;
        return;
      }
      container.innerHTML = '';
      data.forEach(game => {
        const imgSrc = game.resolved_image || game.image_url || '../assets/profile.png';
        const price  = parseFloat(game.price) === 0 ? 'Free' : `$${parseFloat(game.price).toFixed(2)}`;
        const card   = `
<div class="col-md-4 col-sm-6 game-card">
  <div class="card card-glass h-100" onclick="openGameModal('${escAttr(game.title)}', ${game.id})">
    <img src="${escAttr(imgSrc)}" class="card-img-top game-img" alt="${escAttr(game.title)}"
         onerror="this.src='../assets/profile.png'">
    <div class="card-body d-flex flex-column">
      <h5 class="card-title text-white mb-1">${escHtml(game.title)}</h5>
      <span class="genre-tag mb-2">${escHtml(game.genre || 'General')}</span>
      <p class="card-text text-white-50 flex-grow-1" style="font-size:.85rem;line-height:1.4">
        ${escHtml((game.description || '').substring(0, 120))}${game.description && game.description.length > 120 ? '…' : ''}
      </p>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="price-badge">${price}</span>
        <span class="text-white-50" style="font-size:.75rem">Click for details</span>
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

function escHtml(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function escAttr(str) {
  return String(str ?? '').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

document.addEventListener('DOMContentLoaded', fetchGames);
