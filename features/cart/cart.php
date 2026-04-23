<?php
// features/cart/cart.php
// All cart actions: get, count, add, update, remove, clear
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) json_error('Not authenticated', 401);

$user_id = $_SESSION['user_id'];
$action  = $_GET['action'] ?? $_POST['action'] ?? '';

// GET: full cart list
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

// GET: item count badge
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'count') {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetch());
    exit;
}

// GET: just game IDs in cart (used by game detail page)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'ids') {
    $stmt = $pdo->prepare("SELECT game_id FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    exit;
}

// POST: add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $game_id  = intval($_POST['game_id']  ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($game_id < 1 || $quantity < 1) json_error('Invalid parameters');

    $check = $pdo->prepare("SELECT id FROM games WHERE id = ?");
    $check->execute([$game_id]);
    if (!$check->fetch()) json_error('Game not found', 404);

    $pdo->prepare("
        INSERT INTO cart (user_id, game_id, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ")->execute([$user_id, $game_id, $quantity]);

    json_ok(['message' => 'Added to cart']);
}

// POST: update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $cart_id  = intval($_POST['cart_id']  ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    if ($quantity < 1) {
        $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cart_id, $user_id]);
    } else {
        $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?")->execute([$quantity, $cart_id, $user_id]);
    }
    json_ok();
}

// POST: remove one item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'remove') {
    $cart_id = intval($_POST['cart_id'] ?? 0);
    $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cart_id, $user_id]);
    json_ok();
}

// POST: clear entire cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'clear') {
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    json_ok();
}

json_error('Unknown action');
