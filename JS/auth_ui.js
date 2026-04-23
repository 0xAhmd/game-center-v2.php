// JS/auth_ui.js — Injects navbar links based on session state
// All API calls now point to features/ folder

(function () {
  fetch('../features/auth/session_status.php')
    .then(r => r.json())
    .then(renderNav)
    .catch(() => renderNav({ logged_in: false }));

  function renderNav(s) {
    const nav = document.getElementById('navActions');
    if (!nav) return;

    window.__session = s;

    function navAvatar(username, avatarPath) {
      const initials = escHtml(username.slice(0, 2).toUpperCase());
      if (avatarPath) {
        return `
          <div class="gc-nav-avatar-wrap">
            <img src="../${escAttr(avatarPath)}?v=${Date.now()}"
                 alt="${escHtml(username)}"
                 class="gc-nav-avatar"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="gc-nav-avatar-initials" style="display:none">${initials}</div>
          </div>`;
      }
      return `
        <div class="gc-nav-avatar-wrap">
          <div class="gc-nav-avatar-initials">${initials}</div>
        </div>`;
    }

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
        <a href="../HTML/profile.html" class="gc-nav-link gc-nav-profile-link">
          ${navAvatar(s.username, s.avatar_path)}
        </a>
        <a href="../features/auth/logout.php" class="gc-nav-link gc-nav-link-danger">Logout</a>
      `;
      const addBtn = document.getElementById('addGameBtn');
      if (addBtn) addBtn.style.display = 'block';
    } else {
      nav.innerHTML = `
        <a href="../HTML/index.html" class="gc-nav-link">Store</a>
        <a href="../HTML/library.html" class="gc-nav-link gc-nav-library">Library</a>
        <a href="../HTML/cart.html" class="gc-nav-link gc-nav-cart">
          🛒 Cart <span id="cartCountBadge" class="cart-badge"></span>
        </a>
        <a href="../HTML/orders.html" class="gc-nav-link">My Orders</a>
        <a href="../HTML/profile.html" class="gc-nav-link gc-nav-profile-link">
          ${navAvatar(s.username, s.avatar_path)}
        </a>
        <a href="../features/auth/logout.php" class="gc-nav-link gc-nav-link-danger">Logout</a>
      `;
      fetch('../features/cart/cart.php?action=count')
        .then(r => r.json())
        .then(d => {
          const count = parseInt(d.total) || 0;
          const badge = document.getElementById('cartCountBadge');
          if (badge) badge.textContent = count > 0 ? count : '';
        })
        .catch(() => {});
    }

    if (typeof onSessionReady === 'function') onSessionReady(s);
  }

  function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }
  function escAttr(str) {
    return String(str ?? '').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  }
})();
