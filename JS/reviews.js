// JS/reviews.js — Reviews for game detail page

function initReviews(gameId, session) {
  loadReviews(gameId, session);
}

function loadReviews(gameId, session) {
  fetch(`../features/reviews/reviews.php?action=get_reviews_by_game&game_id=${gameId}`)
    .then(r => r.json())
    .then(reviews => renderReviews(reviews, gameId, session))
    .catch(() => {
      document.getElementById('reviewsSection').innerHTML =
        '<p class="text-white-50">Failed to load reviews.</p>';
    });
}

function renderReviews(reviews, gameId, session) {
  const isUser   = session && session.logged_in && session.role !== 'admin';
  const avgRating = reviews.length
    ? (reviews.reduce((s, r) => s + r.rating, 0) / reviews.length).toFixed(1)
    : null;

  const alreadyReviewed = isUser && reviews.some(r => r.username === session.username);

  const starsHtml = (n, interactive = false) => {
    let html = '';
    for (let i = 1; i <= 5; i++) {
      if (interactive) {
        html += `<span class="rv-star rv-star-input" data-val="${i}"
                       onclick="setRating(${i})"
                       onmouseover="hoverRating(${i})"
                       onmouseout="resetHover()">★</span>`;
      } else {
        html += `<span class="rv-star ${i <= n ? 'rv-star-filled' : ''}"
                       style="cursor:default">★</span>`;
      }
    }
    return html;
  };

  const formHtml = isUser && !alreadyReviewed ? `
    <div class="rv-form" id="rvForm">
      <h6 class="text-white fw-bold mb-3">✍️ Write a Review</h6>
      <div class="rv-star-row mb-2" id="rvStarRow">${starsHtml(0, true)}</div>
      <textarea id="rvComment" class="form-control mb-3" rows="3"
                placeholder="Share your thoughts… (optional)" maxlength="1000"></textarea>
      <button class="btn btn-primary btn-sm px-4" onclick="submitReview(${gameId})">
        Submit Review
      </button>
      <div id="rvMsg" class="mt-2" style="font-size:.8rem;min-height:16px"></div>
    </div>` : '';

  const editHint = isUser && alreadyReviewed
    ? `<p class="text-white-50" style="font-size:.8rem">You've already reviewed this game.</p>`
    : '';

  const listHtml = reviews.length ? reviews.map(r => {
    const date    = new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const initials = r.username.slice(0, 2).toUpperCase();
    const avatar   = r.avatar_path
      ? `<img src="../${escHtml(r.avatar_path)}" class="rv-avatar-img" onerror="this.style.display='none';this.nextSibling.style.display='flex'">`
      : '';
    return `
      <div class="rv-item">
        <div class="rv-item-header">
          <div class="rv-avatar">
            ${avatar}
            <span class="rv-avatar-initials" ${r.avatar_path ? 'style="display:none"' : ''}>${initials}</span>
          </div>
          <div>
            <div class="rv-username">${escHtml(r.username)}</div>
            <div class="rv-date">${date}</div>
          </div>
          <div class="rv-stars ms-auto">${starsHtml(r.rating)}</div>
        </div>
        ${r.comment ? `<p class="rv-comment">${escHtml(r.comment)}</p>` : ''}
      </div>`;
  }).join('') : `<p class="text-white-50">No reviews yet. Be the first!</p>`;

  document.getElementById('reviewsSection').innerHTML = `
    <div class="rv-header">
      <h5 class="text-white fw-bold mb-0">⭐ Reviews</h5>
      ${avgRating ? `<span class="rv-avg">${avgRating} / 5 <span style="color:rgba(255,255,255,.4);font-size:.8rem">(${reviews.length})</span></span>` : ''}
    </div>
    ${formHtml}
    ${editHint}
    <div class="rv-list" id="rvList">${listHtml}</div>`;

  window._rvRating = 0;
}

window.setRating = function(val) {
  window._rvRating = val;
  document.querySelectorAll('.rv-star-input').forEach(s => {
    s.classList.toggle('rv-star-filled', parseInt(s.dataset.val) <= val);
  });
};

window.hoverRating = function(val) {
  document.querySelectorAll('.rv-star-input').forEach(s => {
    s.classList.toggle('rv-star-hover', parseInt(s.dataset.val) <= val);
  });
};

window.resetHover = function() {
  document.querySelectorAll('.rv-star-input').forEach(s => s.classList.remove('rv-star-hover'));
};

window.submitReview = function(gameId) {
  const rating  = window._rvRating || 0;
  const comment = document.getElementById('rvComment')?.value.trim() || '';
  const msgEl   = document.getElementById('rvMsg');

  if (rating < 1) { msgEl.innerHTML = '<span style="color:#ffc107">Please select a star rating.</span>'; return; }

  const fd = new FormData();
  fd.append('action',  'add_review');
  fd.append('game_id', gameId);
  fd.append('rating',  rating);
  fd.append('comment', comment);

  fetch('../features/reviews/reviews.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        msgEl.innerHTML = '<span style="color:#28a865">✅ Review submitted!</span>';
        setTimeout(() => loadReviews(gameId, window.__session), 800);
      } else {
        msgEl.innerHTML = `<span style="color:#ff9d9d">⚠️ ${escHtml(d.error)}</span>`;
      }
    })
    .catch(() => { msgEl.innerHTML = '<span style="color:#ff9d9d">Request failed.</span>'; });
};

function escHtml(s) { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }