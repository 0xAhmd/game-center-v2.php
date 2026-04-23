// JS/admin.js — Admin dashboard
// Updated to use features/ paths

let currentOrderId = null;

fetch('../features/auth/session_status.php')
  .then(r => r.json())
  .then(s => {
    if (!s.logged_in || s.role !== 'admin') {
      window.location.href = 'login.html';
      return;
    }
    loadStats();
    loadAdminOrders();
  });

function loadStats() {
  Promise.all([
    fetch('../features/games/get_games.php').then(r => r.json()),
    fetch('../features/admin/users.php?action=list').then(r => r.json()),
    fetch('../features/orders/orders.php?action=all').then(r => r.json()),
  ]).then(([games, users, orders]) => {
    document.getElementById('statGames').textContent  = Array.isArray(games)  ? games.length  : '—';
    document.getElementById('statUsers').textContent  = Array.isArray(users)  ? users.length  : '—';
    document.getElementById('statOrders').textContent = Array.isArray(orders) ? orders.length : '—';
    const revenue = Array.isArray(orders)
      ? orders.reduce((sum, o) => sum + parseFloat(o.total_price || 0), 0) : 0;
    document.getElementById('statRevenue').textContent = '$' + revenue.toFixed(2);
  }).catch(err => console.error('Stats error:', err));
}

function showTab(tab) {
  ['orders', 'users'].forEach(t => {
    document.getElementById('tab-' + t).style.display = t === tab ? '' : 'none';
  });
  document.querySelectorAll('#adminTabs .nav-link').forEach((btn, i) => {
    btn.classList.toggle('active', ['orders','users'][i] === tab);
  });
  if (tab === 'users') loadAdminUsers();
}

function loadAdminOrders() {
  fetch('../features/orders/orders.php?action=all')
    .then(r => r.json())
    .then(renderAdminOrders)
    .catch(() => {
      document.getElementById('adminOrdersContainer').innerHTML =
        '<p class="text-white-50">Failed to load orders.</p>';
    });
}

function renderAdminOrders(orders) {
  const c = document.getElementById('adminOrdersContainer');
  if (!orders.length) { c.innerHTML = '<p class="text-white-50">No orders yet.</p>'; return; }

  const statusColors = { pending:'status-pending', processing:'status-processing', completed:'status-completed', cancelled:'status-cancelled' };
  let html = `<div class="orders-table-wrap"><table class="orders-table">
    <thead><tr><th>#</th><th>Customer</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>`;
  orders.forEach(o => {
    const date = new Date(o.created_at).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
    const avatarHtml    = o.avatar_path ? `<img src="../${escAttr(o.avatar_path)}" class="user-mini-avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">` : '';
    const initialsHtml  = `<span class="user-mini-initials" ${o.avatar_path ? 'style="display:none"' : ''}>${escHtml((o.username||'?').slice(0,2).toUpperCase())}</span>`;
    html += `<tr>
      <td class="text-white-50">#${o.id}</td>
      <td class="text-white">
        <div class="user-cell">
          <div class="user-mini-avatar-wrap">${avatarHtml}${initialsHtml}</div>
          <div><div>${escHtml(o.username)}</div><small class="text-white-50">${escHtml(o.email)}</small></div>
        </div>
      </td>
      <td class="text-white-50">${date}</td>
      <td class="text-white">${o.item_count}</td>
      <td class="text-white fw-bold">$${parseFloat(o.total_price).toFixed(2)}</td>
      <td><span class="order-status ${statusColors[o.status]||''}">${o.status}</span></td>
      <td><button class="btn btn-sm btn-outline-light" onclick="viewOrderDetail(${o.id},'${o.status}')">Details</button></td>
    </tr>`;
  });
  html += '</tbody></table></div>';
  c.innerHTML = html;
}

function loadAdminUsers() {
  fetch('../features/admin/users.php?action=list')
    .then(r => r.json())
    .then(renderAdminUsers)
    .catch(() => {
      document.getElementById('adminUsersContainer').innerHTML =
        '<p class="text-white-50">Failed to load users.</p>';
    });
}

function renderAdminUsers(users) {
  const c = document.getElementById('adminUsersContainer');
  if (!users.length) { c.innerHTML = '<p class="text-white-50">No users yet.</p>'; return; }

  let html = `<div class="orders-table-wrap"><table class="orders-table">
    <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead><tbody>`;

  users.forEach(u => {
    const date = new Date(u.created_at).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
    const avatarHtml   = u.avatar_path ? `<img src="../${escAttr(u.avatar_path)}" class="user-mini-avatar" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">` : '';
    const initialsHtml = `<span class="user-mini-initials" ${u.avatar_path ? 'style="display:none"' : ''}>${escHtml((u.username||'?').slice(0,2).toUpperCase())}</span>`;
    const roleBadge    = u.role === 'admin'
      ? `<span class="role-badge role-admin">⚙️ Admin</span>`
      : `<span class="role-badge role-user">🎮 User</span>`;

    html += `<tr class="user-row-clickable" onclick="goToUserProfile(${u.id})" style="cursor:pointer">
      <td>
        <div class="user-cell">
          <div class="user-mini-avatar-wrap">${avatarHtml}${initialsHtml}</div>
          <div><div class="text-white fw-bold">${escHtml(u.username)}</div><small class="text-white-50">ID #${u.id}</small></div>
        </div>
      </td>
      <td class="text-white-50">${escHtml(u.email)}</td>
      <td>
        <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
          ${roleBadge}
          <select class="form-select form-select-sm w-auto d-inline-block" onchange="updateRole(${u.id}, this.value)">
            <option value="user"  ${u.role==='user'  ?'selected':''}>User</option>
            <option value="admin" ${u.role==='admin' ?'selected':''}>Admin</option>
          </select>
        </div>
      </td>
      <td class="text-white-50">${date}</td>
      <td onclick="event.stopPropagation()">
        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-light" onclick="goToUserProfile(${u.id})">View Profile</button>
          <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${u.id})">Delete</button>
        </div>
      </td>
    </tr>`;
  });

  html += '</tbody></table></div>';
  c.innerHTML = html;
  injectRowHoverStyle();
}

function injectRowHoverStyle() {
  if (document.getElementById('userRowHoverStyle')) return;
  const style = document.createElement('style');
  style.id = 'userRowHoverStyle';
  style.textContent = `.user-row-clickable:hover td { background: rgba(108,60,225,0.12) !important; }`;
  document.head.appendChild(style);
}

function goToUserProfile(userId) {
  window.location.href = `../HTML/admin_user_profile.html?id=${userId}`;
}

function updateRole(userId, role) {
  const fd = new FormData();
  fd.append('action', 'update_role');
  fd.append('user_id', userId);
  fd.append('role', role);
  fetch('../features/admin/users.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (!d.success) alert(d.error); else loadStats(); })
    .catch(() => alert('Failed to update role.'));
}

function deleteUser(userId) {
  if (!confirm('Delete this user? This will remove all their data.')) return;
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('user_id', userId);
  fetch('../features/admin/users.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => { if (d.success) { loadAdminUsers(); loadStats(); } else alert(d.error); });
}

function viewOrderDetail(orderId, currentStatus) {
  currentOrderId = orderId;
  document.getElementById('orderDetailBody').innerHTML =
    '<div class="text-center py-3"><div class="spinner-border text-light"></div></div>';
  const sel = document.getElementById('statusSelect');
  if (sel) sel.value = currentStatus;
  new bootstrap.Modal(document.getElementById('orderDetailModal')).show();

  fetch(`../features/orders/orders.php?action=detail&order_id=${orderId}`)
    .then(r => r.json())
    .then(items => {
      const body = document.getElementById('orderDetailBody');
      if (!items.length) { body.innerHTML = '<p class="text-white-50">No items found.</p>'; return; }
      let html = '<div class="order-items-list">';
      items.forEach(item => {
        const imgSrc = item.image_path ? '../' + item.image_path : item.image_url;
        html += `<div class="order-detail-item">
          <img src="${escAttr(imgSrc)}" class="order-item-img" onerror="this.src='../assets/profile.png'">
          <div class="order-item-info">
            <div class="text-white fw-bold">${escHtml(item.title)}</div>
            <div class="text-white-50">Qty: ${item.quantity} × $${parseFloat(item.price).toFixed(2)}</div>
          </div>
          <div class="text-white fw-bold">$${(item.quantity * item.price).toFixed(2)}</div>
        </div>`;
      });
      body.innerHTML = html + '</div>';
    });
}

function updateOrderStatus() {
  if (!currentOrderId) return;
  const status = document.getElementById('statusSelect').value;
  const fd = new FormData();
  fd.append('action', 'update_status');
  fd.append('order_id', currentOrderId);
  fd.append('status', status);
  fetch('../features/orders/orders.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        bootstrap.Modal.getInstance(document.getElementById('orderDetailModal')).hide();
        loadAdminOrders();
        loadStats();
      }
    });
}

function escHtml(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function escAttr(s) { return String(s??'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
