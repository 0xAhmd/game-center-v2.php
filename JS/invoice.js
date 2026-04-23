// JS/invoice.js — Order Invoice Page

(function () {
  const params  = new URLSearchParams(window.location.search);
  const orderId = parseInt(params.get('order_id'));

  // Wait for auth_ui.js to resolve the session, then load
  window.onSessionReady = (function (_prev) {
    return function (session) {
      if (typeof _prev === 'function') _prev(session);
      if (!session.logged_in) {
        window.location.href = 'login.html';
        return;
      }
      if (!orderId) {
        renderError('No order ID specified.');
        return;
      }
      loadInvoice(session);
    };
  })(window.onSessionReady);

  function loadInvoice(session) {
    // Fetch order header (status, total, date) + line items in parallel
    const headerP = fetch('../features/orders/orders.php?action=my_orders')
      .then(r => r.json());
    const itemsP  = fetch(`../features/orders/orders.php?action=detail&order_id=${orderId}`)
      .then(r => r.json());

    Promise.all([headerP, itemsP])
      .then(([orders, items]) => {
        const order = Array.isArray(orders)
          ? orders.find(o => parseInt(o.id) === orderId)
          : null;

        if (!order) {
          renderError('Order not found or you do not have access to this invoice.');
          return;
        }
        if (!Array.isArray(items) || items.length === 0) {
          renderError('No items found for this order.');
          return;
        }

        renderInvoice(order, items, session);
      })
      .catch(() => renderError('Failed to load invoice. Please try again.'));
  }

  function renderInvoice(order, items, session) {
    const wrap = document.getElementById('invoiceWrap');

    const date = new Date(order.created_at).toLocaleDateString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric'
    });

    const statusColors = {
      pending:    'status-pending',
      processing: 'status-processing',
      completed:  'status-completed',
      cancelled:  'status-cancelled',
    };

    // Line items HTML
    const itemsHtml = items.map(item => {
      const imgSrc   = item.image_path ? '../' + item.image_path : (item.image_url || '../assets/profile.png');
      const lineTotal = (item.quantity * parseFloat(item.price)).toFixed(2);
      return `
        <div class="invoice-item">
          <img src="${escAttr(imgSrc)}" alt="${escAttr(item.title)}"
               class="invoice-item-img"
               onerror="this.src='../assets/profile.png'">
          <div class="invoice-item-info">
            <div class="invoice-item-title">${escHtml(item.title)}</div>
            <div class="invoice-item-qty">Qty: ${item.quantity} × $${parseFloat(item.price).toFixed(2)}</div>
          </div>
          <div class="invoice-item-price">$${lineTotal}</div>
        </div>`;
    }).join('');

    wrap.innerHTML = `
      <!-- Thank-you banner -->
      <div class="invoice-banner">
        <div class="invoice-banner-icon">🎉</div>
        <h1>Thank You, ${escHtml(session.username)}!</h1>
        <p>Your order has been received. Here's your invoice.</p>
      </div>

      <!-- Order summary card -->
      <div class="invoice-card">
        <div class="invoice-card-header">
          <span class="invoice-card-title">🧾 Order Summary</span>
          <span class="order-status ${statusColors[order.status] || ''}">${escHtml(order.status)}</span>
        </div>

        <!-- Meta -->
        <div class="invoice-meta">
          <div class="invoice-meta-item">
            <span class="invoice-meta-label">Order ID</span>
            <span class="invoice-meta-value">#${order.id}</span>
          </div>
          <div class="invoice-meta-item">
            <span class="invoice-meta-label">Date</span>
            <span class="invoice-meta-value">${date}</span>
          </div>
          <div class="invoice-meta-item">
            <span class="invoice-meta-label">Items</span>
            <span class="invoice-meta-value">${items.length} game${items.length !== 1 ? 's' : ''}</span>
          </div>
        </div>

        <!-- Line items -->
        <div class="invoice-items">${itemsHtml}</div>

        <!-- Total -->
        <div class="invoice-total">
          <span class="invoice-total-label">Total</span>
          <span class="invoice-total-value">$${parseFloat(order.total_price).toFixed(2)}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="invoice-actions">
        <a href="orders.html" class="btn btn-outline-light px-4">📦 My Orders</a>
        <a href="index.html"  class="btn btn-primary px-4">🎮 Continue Shopping</a>
      </div>`;

    document.title = `Invoice #${order.id} — Game Center`;
  }

  function renderError(msg) {
    document.getElementById('invoiceWrap').innerHTML = `
      <div class="invoice-error">
        <p class="mb-3">⚠️ ${escHtml(msg)}</p>
        <a href="orders.html" class="btn btn-outline-light btn-sm">← Back to Orders</a>
      </div>`;
  }

  function escHtml(s)  { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
  function escAttr(s)  { return String(s ?? '').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
})();
