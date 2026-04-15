<?php
// scripts/profile.php — User profile API (JSON)
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

// ── GET: profile info + stats ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $stmt = $pdo->prepare("SELECT id, username, email, role, avatar_path, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user) { http_response_code(404); echo json_encode(['error' => 'User not found']); exit; }

    // Order stats
    $statsStmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total_orders,
            COALESCE(SUM(total_price), 0) AS total_spent,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) AS completed_orders,
            COALESCE(SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END), 0) AS pending_orders
        FROM orders WHERE user_id = ?
    ");
    $statsStmt->execute([$user_id]);
    $stats = $statsStmt->fetch();

    // Recent orders
    $recentStmt = $pdo->prepare("
        SELECT o.id, o.total_price, o.status, o.created_at,
               COUNT(oi.id) AS item_count
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT 3
    ");
    $recentStmt->execute([$user_id]);
    $recentOrders = $recentStmt->fetchAll();

    echo json_encode([
        'user'          => $user,
        'stats'         => $stats,
        'recent_orders' => $recentOrders,
    ]);
    exit;
}

// ── POST: update profile (username / email) ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');

    if (strlen($username) < 3 || strlen($username) > 50) {
        echo json_encode(['error' => 'Username must be 3–50 characters.']); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email address.']); exit;
    }

    // Check uniqueness (exclude self)
    $check = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
    $check->execute([$email, $username, $user_id]);
    if ($check->fetch()) {
        echo json_encode(['error' => 'Username or email already taken.']); exit;
    }

    $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?")
        ->execute([$username, $email, $user_id]);

    // Update session
    $_SESSION['username'] = $username;

    echo json_encode(['success' => true, 'username' => $username]);
    exit;
}

// ── POST: change password ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change_password') {
    $current  = $_POST['current_password']  ?? '';
    $new      = $_POST['new_password']      ?? '';
    $confirm  = $_POST['confirm_password']  ?? '';

    if (strlen($new) < 6) {
        echo json_encode(['error' => 'New password must be at least 6 characters.']); exit;
    }
    if ($new !== $confirm) {
        echo json_encode(['error' => 'New passwords do not match.']); exit;
    }

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password'])) {
        echo json_encode(['error' => 'Current password is incorrect.']); exit;
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $user_id]);

    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
