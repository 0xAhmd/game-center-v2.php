<?php
// scripts/orders.php — Checkout & Orders API (JSON)
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action  = $_GET['action'] ?? $_POST['action'] ?? '';

// ── GET: my orders ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'my_orders') {
    $stmt = $pdo->prepare("
        SELECT o.id, o.total_price, o.status, o.created_at,
               COUNT(oi.id) AS item_count
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// ── GET: order detail ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'detail') {
    $order_id = intval($_GET['order_id'] ?? 0);
    $ownerCheck = $pdo->prepare("SELECT user_id FROM orders WHERE id = ?");
    $ownerCheck->execute([$order_id]);
    $order = $ownerCheck->fetch();
    if (!$order || ($order['user_id'] != $user_id && !is_admin())) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
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

// ── GET: all orders (admin only) ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'all') {
    if (!is_admin()) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
    $stmt = $pdo->query("
        SELECT o.id, o.total_price, o.status, o.created_at,
               u.username, u.email, u.avatar_path,
               COUNT(oi.id) AS item_count
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// ── POST: checkout ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'checkout') {
    $stmt = $pdo->prepare("
        SELECT c.game_id, c.quantity, g.price
        FROM cart c
        JOIN games g ON c.game_id = g.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();

    if (empty($items)) {
        http_response_code(400);
        echo json_encode(['error' => 'Cart is empty']);
        exit;
    }

    $total = array_reduce($items, fn($carry, $i) => $carry + ($i['price'] * $i['quantity']), 0);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, game_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $itemStmt->execute([$order_id, $item['game_id'], $item['quantity'], $item['price']]);
        }

        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'order_id' => $order_id, 'total' => $total]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Checkout failed: ' . $e->getMessage()]);
    }
    exit;
}

// ── POST: update order status (admin) ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status') {
    if (!is_admin()) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }

    $order_id = intval($_POST['order_id'] ?? 0);
    $status   = $_POST['status'] ?? '';
    $allowed  = ['pending','processing','completed','cancelled'];

    if (!in_array($status, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid status']);
        exit;
    }

    // Fetch current status before updating
    $currentStmt = $pdo->prepare("SELECT status, user_id FROM orders WHERE id = ?");
    $currentStmt->execute([$order_id]);
    $currentOrder = $currentStmt->fetch();

    if (!$currentOrder) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $order_id]);

    // ── Auto-add games to library when order is marked completed ─────────────
    // Only trigger if transitioning INTO completed (not already completed)
    if ($status === 'completed' && $currentOrder['status'] !== 'completed') {
        $order_user_id = (int) $currentOrder['user_id'];

        // Get all games in this order
        $itemsStmt = $pdo->prepare("SELECT game_id FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$order_id]);
        $games = $itemsStmt->fetchAll();

        // Insert into user_library, skip duplicates
        $libStmt = $pdo->prepare("
            INSERT IGNORE INTO user_library (user_id, game_id)
            VALUES (?, ?)
        ");
        foreach ($games as $game) {
            $libStmt->execute([$order_user_id, $game['game_id']]);
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
