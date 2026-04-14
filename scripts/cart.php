<?php
// scripts/cart.php — Cart CRUD API (JSON)
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

// ── GET cart ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $stmt = $pdo->prepare("
        SELECT c.id, c.game_id, c.quantity,
               g.title, g.price, g.image_url, g.image_path
        FROM cart c
        JOIN games g ON c.game_id = g.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// ── GET cart count ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'count') {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetch());
    exit;
}

// ── POST: add ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $game_id  = intval($_POST['game_id']  ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($game_id < 1 || $quantity < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
        exit;
    }
    // Verify game exists
    $check = $pdo->prepare("SELECT id FROM games WHERE id = ?");
    $check->execute([$game_id]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Game not found']);
        exit;
    }
    // Upsert
    $stmt = $pdo->prepare("
        INSERT INTO cart (user_id, game_id, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $stmt->execute([$user_id, $game_id, $quantity]);
    echo json_encode(['success' => true, 'message' => 'Added to cart']);
    exit;
}

// ── POST: update quantity ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $cart_id  = intval($_POST['cart_id']  ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($quantity < 1) {
        // treat as remove
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$quantity, $cart_id, $user_id]);
    }
    echo json_encode(['success' => true]);
    exit;
}

// ── POST: remove ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'remove') {
    $cart_id = intval($_POST['cart_id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    echo json_encode(['success' => true]);
    exit;
}

// ── POST: clear ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'clear') {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
