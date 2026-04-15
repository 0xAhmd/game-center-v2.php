// JS/auth_ui.js — Injects navbar links based on session state
// Included on every page

(function () {
  fetch('../scripts/session_status.php')
    .then(r => r.json())
    .then(renderNav)
    .catch(() => renderNav({ logged_in: false }));

  function renderNav(s) {
    const nav = document.getElementById('navActions');
    if (!nav) return;

    // expose globally
    window.__session = s;

    if (!s.logged_in) {
      nav.innerHTML = `
        <a href="../HTML/login.html" class="gc-nav-link">Sign In</a>
        <a href="../HTML/register.html" class="gc-nav-link gc-nav-link-primary">Register</a>
      `;
    } else if (s.role === 'admin') {
      nav.innerHTML = `
        <a href="../HTML/index.html" class="gc-nav-link">Store</a>
        <a href="../HTML/admin.html" class="gc-nav-link">Dashboard</a>
        <a href="../HTML/orders.html" class="gc-nav-link">Orders</a>
        <a href="../HTML/profile.html" class="gc-nav-link gc-nav-profile">👤 ${escHtml(s.username)}</a>
        <a href="../scripts/logout.php" class="gc-nav-link gc-nav-link-danger">Logout</a>
      `;
      // Show floating add button if present
      const addBtn = document.getElementById('addGameBtn');
      if (addBtn) addBtn.style.display = 'block';
    } else {
      nav.innerHTML = `
        <a href="../HTML/index.html" class="gc-nav-link">Store</a>
        <a href="../HTML/cart.html" class="gc-nav-link gc-nav-cart">
          🛒 Cart <span id="cartCountBadge" class="cart-badge"></span>
        </a>
        <a href="../HTML/orders.html" class="gc-nav-link">My Orders</a>
        <a href="../HTML/profile.html" class="gc-nav-link gc-nav-profile">👤 ${escHtml(s.username)}</a>
        <a href="../scripts/logout.php" class="gc-nav-link gc-nav-link-danger">Logout</a>
      `;
      // Trigger cart count fetch
      fetch('../scripts/cart.php?action=count')
        .then(r => r.json())
        .then(d => {
          const count = parseInt(d.total) || 0;
          const badge = document.getElementById('cartCountBadge');
          if (badge) badge.textContent = count > 0 ? count : '';
        })
        .catch(() => {});
    }

    // Expose session to modal logic in scripts.js
    if (typeof onSessionReady === 'function') onSessionReady(s);
  }

  function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
})();