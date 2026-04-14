// JS/cart.js — Cart page logic

let cartData = [];
let currentOrderTotal = 0;

// ── Guard: require login ──────────────────────────────────────────────────
fetch('../scripts/session_status.php')
  .then(r => r.json())
  .then(s => {
    if (!s.logged_in) { window.location.href = 'login.html'; return; }
    loadCart();
  });

function loadCart() {
  fetch('../scripts/cart.php?action=get')
    .then(r => r.json())
    .then(data => {
      cartData = data;
      renderCart(data);
    })
    .catch(() => {
      document.getElementById('cartContainer').innerHTML =
        `<div class="alert alert-danger">Failed to load cart.</div>`;
    });
}

function renderCart(items) {
  const container = document.getElementById('cartContainer');

  if (!items.length) {
    container.innerHTML = `
      <div class="empty-state">
        <div class="empty-icon">🛒</div>
        <h4 class="text-white">Your cart is empty</h4>
        <p class="text-white-50">Browse the store and add some games!</p>
        <a href="index.html" class="btn btn-primary mt-3">Browse Games</a>
      </div>`;
    return;
  }

  let total = 0;
  let rows  = '';

  items.forEach(item => {
    const price    = parseFloat(item.price);
    const subtotal = price * item.quantity;
    total += subtotal;
    const imgSrc   = item.image_path ? '../' + item.image_path : item.image_url;

    rows += `
<div class="cart-item" id="cartItem_${item.id}">
  <img src="${escAttr(imgSrc)}" alt="${escAttr(item.title)}" class="cart-item-img"
       onerror="this.src='../assets/profile.png'">
  <div class="cart-item-info">
    <div class="cart-item-title">${escHtml(item.title)}</div>
    <div class="cart-item-price">${price === 0 ? 'Free' : '$' + price.toFixed(2)} each</div>
  </div>
  <div class="cart-item-controls">
    <div class="qty-control">
      <button onclick="changeQty(${item.id}, ${item.quantity - 1})" class="qty-btn">−</button>
      <span class="qty-val">${item.quantity}</span>
      <button onclick="changeQty(${item.id}, ${item.quantity + 1})" class="qty-btn">+</button>
    </div>
    <div class="cart-item-subtotal">$${subtotal.toFixed(2)}</div>
    <button onclick="removeItem(${item.id})" class="btn-remove" title="Remove">✕</button>
  </div>
</div>`;
  });

  currentOrderTotal = total;

  container.innerHTML = `
    <div class="cart-items-list">${rows}</div>
    <div class="cart-summary">
      <div class="cart-summary-row">
        <span class="text-white-50">Items (${items.length})</span>
        <span class="text-white">$${total.toFixed(2)}</span>
      </div>
      <div class="cart-summary-row cart-total-row">
        <span class="text-white fw-bold fs-5">Total</span>
        <span class="price-badge fs-5">$${total.toFixed(2)}</span>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-success flex-grow-1" onclick="openCheckout()">
          ✅ Checkout
        </button>
        <button class="btn btn-outline-danger" onclick="clearCart()">Clear Cart</button>
      </div>
    </div>`;
}

function changeQty(cartId, newQty) {
  const fd = new FormData();
  fd.append('action', 'update');
  fd.append('cart_id', cartId);
  fd.append('quantity', newQty);
  fetch('../scripts/cart.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(() => loadCart());
}

function removeItem(cartId) {
  const fd = new FormData();
  fd.append('action', 'remove');
  fd.append('cart_id', cartId);
  fetch('../scripts/cart.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(() => loadCart());
}

function clearCart() {
  if (!confirm('Clear entire cart?')) return;
  const fd = new FormData();
  fd.append('action', 'clear');
  fetch('../scripts/cart.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(() => loadCart());
}

function openCheckout() {
  document.getElementById('checkoutTotal').textContent = '$' + currentOrderTotal.toFixed(2);
  new bootstrap.Modal(document.getElementById('checkoutModal')).show();
}

function confirmCheckout() {
  const fd = new FormData();
  fd.append('action', 'checkout');
  fetch('../scripts/orders.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
      if (d.success) {
        document.getElementById('cartContainer').innerHTML = `
          <div class="empty-state">
            <div class="empty-icon">🎉</div>
            <h4 class="text-white">Order Placed!</h4>
            <p class="text-white-50">Order #${d.order_id} — Total: $${parseFloat(d.total).toFixed(2)}</p>
            <a href="orders.html" class="btn btn-primary mt-3">View My Orders</a>
            <a href="index.html" class="btn btn-outline-light mt-3 ms-2">Continue Shopping</a>
          </div>`;
      } else {
        alert(d.error || 'Checkout failed.');
      }
    });
}

function escHtml(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function escAttr(s) { return String(s??'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
