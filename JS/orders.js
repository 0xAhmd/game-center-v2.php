// JS/orders.js — Orders page
// Updated to use features/ paths

let currentOrderId = null;
let isAdminView    = false;

fetch('../features/auth/session_status.php')
  .then(r => r.json())
  .then(s => {
    if (!s.logged_in) { window.location.href = 'login.html'; return; }
    isAdminView = s.role === 'admin';
    if (isAdminView) {
      document.getElementById('ordersHeading').textContent = '📦 All Orders';
      document.getElementById('adminStatusFooter').style.display = '';
    }
    loadOrders(isAdminView);
  });

function loadOrders(asAdmin) {
  const url = asAdmin
    ? '../features/orders/orders.php?action=all'
    : '../features/orders/orders.php?action=my_orders';

  fetch(url)
    .then(r => r.json())
    .then(data => renderOrders(data, asAdmin))
    .catch(() => {
      document.getElementById('ordersContainer').innerHTML =
        `<div class="alert alert-danger">Failed to load orders.</div>`;
    });
}

function renderOrders(orders, asAdmin) {
  const container = document.getElementById('ordersContainer');
  if (!orders.length) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h4 class="text-white">No orders yet</h4>
        <p class="text-white-50">${asAdmin ? 'No orders placed yet.' : 'Place your first order!'}</p>
        ${!asAdmin ? '<a href="index.html" class="btn btn-primary mt-3">Browse Games</a>' : ''}
      </div>`;
    return;
  }

  const statusColors = {
    pending:    'status-pending',
    processing: 'status-processing',
    completed:  'status-completed',
    cancelled:  'status-cancelled'
  };

  let html = `<div class="orders-table-wrap"><table class="orders-table">
    <thead><tr>
      <th>#</th>
      ${asAdmin ? '<th>Customer</th>' : ''}
      <th>Date</th><th>Items</th><th>Total</th><th>Status</th><th></th>
    </tr></thead><tbody>`;

  orders.forEach(o => {
    const date = new Date(o.created_at).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
    html += `<tr>
      <td class="text-white-50">#${o.id}</td>
      ${asAdmin ? `<td class="text-white">${escHtml(o.username)}<br><small class="text-white-50">${escHtml(o.email)}</small></td>` : ''}
      <td class="text-white-50">${date}</td>
      <td class="text-white">${o.item_count} game${o.item_count != 1 ? 's' : ''}</td>
      <td class="text-white fw-bold">$${parseFloat(o.total_price).toFixed(2)}</td>
      <td><span class="order-status ${statusColors[o.status] || ''}">${o.status}</span></td>
      <td><button class="btn btn-sm btn-outline-light" onclick="viewOrderDetail(${o.id}, '${o.status}')">Details</button></td>
    </tr>`;
  });

  html += '</tbody></table></div>';
  container.innerHTML = html;
}

function viewOrderDetail(orderId, currentStatus) {
  currentOrderId = orderId;
  const body = document.getElementById('orderDetailBody');
  body.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-light"></div></div>';
  const sel = document.getElementById('statusSelect');
  if (sel) sel.value = currentStatus;
  new bootstrap.Modal(document.getElementById('orderDetailModal')).show();

  fetch(`../features/orders/orders.php?action=detail&order_id=${orderId}`)
    .then(r => r.json())
    .then(items => {
      if (!items.length) { body.innerHTML = '<p class="text-white-50">No items found.</p>'; return; }
      let html = `<div class="order-items-list">`;
      items.forEach(item => {
        const imgSrc = item.image_path ? '../' + item.image_path : item.image_url;
        html += `
          <div class="order-detail-item">
            <img src="${escAttr(imgSrc)}" alt="${escAttr(item.title)}" class="order-item-img"
                 onerror="this.src='../assets/profile.png'">
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
        loadOrders(isAdminView);
      }
    });
}

function escHtml(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function escAttr(s) { return String(s??'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
