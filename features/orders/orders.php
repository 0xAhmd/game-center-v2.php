<?php
// features/orders/orders.php
// Actions: my_orders, all (admin), detail, checkout, update_status (admin)
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) json_error('Not authenticated', 401);

$user_id = $_SESSION['user_id'];
$action  = $_GET['action'] ?? $_POST['action'] ?? '';

// GET: current user's orders
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'my_orders') {
    $stmt = $pdo->prepare("
SELECT o.id, o.total_price, o.status, o.created_at,
       COUNT(oi.id) AS item_count,
       GROUP_CONCAT(g.title ORDER BY g.title SEPARATOR ', ') AS game_titles
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN games g ON oi.game_id = g.id
WHERE o.user_id = ?
GROUP BY o.id
ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// GET: single order detail (user sees own, admin sees all)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'detail') {
    $order_id = intval($_GET['order_id'] ?? 0);
    $ownerCheck = $pdo->prepare("SELECT user_id FROM orders WHERE id = ?");
    $ownerCheck->execute([$order_id]);
    $order = $ownerCheck->fetch();
    if (!$order || ($order['user_id'] != $user_id && !is_admin())) {
        json_error('Forbidden', 403);
    }
    $stmt = $pdo->prepare("
        SELECT oi.quantity, oi.price, g.title, g.image_url, g.image_path
        FROM order_items oi
        JOIN games g ON oi.game_id = g.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// GET: all orders — admin only
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'all') {
    if (!is_admin()) json_error('Forbidden', 403);
    $stmt = $pdo->query("
SELECT o.id, o.total_price, o.status, o.created_at,
       u.username, u.email, u.avatar_path,
       COUNT(oi.id) AS item_count,
       GROUP_CONCAT(g.title ORDER BY g.title SEPARATOR ', ') AS game_titles
FROM orders o
JOIN users u ON o.user_id = u.id
JOIN order_items oi ON o.id = oi.order_id
JOIN games g ON oi.game_id = g.id
GROUP BY o.id
ORDER BY o.created_at DESC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST: checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'checkout') {
    $stmt = $pdo->prepare("
        SELECT c.game_id, c.quantity, g.price
        FROM cart c JOIN games g ON c.game_id = g.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();

    if (empty($items)) json_error('Cart is empty');

    $total = array_reduce($items, fn($carry, $i) => $carry + ($i['price'] * $i['quantity']), 0);

    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')")
            ->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, game_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $itemStmt->execute([$order_id, $item['game_id'], $item['quantity'], $item['price']]);
        }

        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
        $pdo->commit();
        json_ok(['order_id' => $order_id, 'total' => $total]);
    } catch (Exception $e) {
        $pdo->rollBack();
        json_error('Checkout failed: ' . $e->getMessage(), 500);
    }
}

// POST: update order status — admin only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    if (!is_admin()) json_error('Forbidden', 403);

    $order_id = intval($_POST['order_id'] ?? 0);
    $status   = $_POST['status'] ?? '';
    $allowed  = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($status, $allowed)) json_error('Invalid status');

    // Get current status before update
    $curr = $pdo->prepare("SELECT status, user_id FROM orders WHERE id = ?");
    $curr->execute([$order_id]);
    $currentOrder = $curr->fetch();
    if (!$currentOrder) json_error('Order not found', 404);

    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $order_id]);

    // Auto-add games to library when marked completed
    if ($status === 'completed' && $currentOrder['status'] !== 'completed') {
        $order_user_id = (int) $currentOrder['user_id'];
        $gamesStmt = $pdo->prepare("SELECT game_id FROM order_items WHERE order_id = ?");
        $gamesStmt->execute([$order_id]);
        $libStmt = $pdo->prepare("INSERT IGNORE INTO user_library (user_id, game_id) VALUES (?, ?)");
        foreach ($gamesStmt->fetchAll() as $game) {
            $libStmt->execute([$order_user_id, $game['game_id']]);
        }
    }

    json_ok();
}

json_error('Unknown action');
