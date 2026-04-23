<?php
// features/profile/profile.php
// Actions: get, update, change_password
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) json_error('Not authenticated', 401);

$user_id = $_SESSION['user_id'];
$action  = $_GET['action'] ?? $_POST['action'] ?? '';

// GET: profile info + stats + recent orders
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $stmt = $pdo->prepare("SELECT id, username, email, role, avatar_path, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user) json_error('User not found', 404);

    $statsStmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total_orders,
            COALESCE(SUM(total_price), 0) AS total_spent,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) AS completed_orders,
            COALESCE(SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END), 0) AS pending_orders
        FROM orders WHERE user_id = ?
    ");
    $statsStmt->execute([$user_id]);

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

    echo json_encode([
        'user'          => $user,
        'stats'         => $statsStmt->fetch(),
        'recent_orders' => $recentStmt->fetchAll(),
    ]);
    exit;
}

// POST: update username/email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');

    if (strlen($username) < 3 || strlen($username) > 50) json_error('Username must be 3–50 characters.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))       json_error('Invalid email address.');

    $check = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
    $check->execute([$email, $username, $user_id]);
    if ($check->fetch()) json_error('Username or email already taken.');

    $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?")->execute([$username, $email, $user_id]);
    $_SESSION['username'] = $username;
    json_ok(['username' => $username]);
}

// POST: change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change_password') {
    $current = $_POST['current_password']  ?? '';
    $new     = $_POST['new_password']      ?? '';
    $confirm = $_POST['confirm_password']  ?? '';

    if (strlen($new) < 6)      json_error('New password must be at least 6 characters.');
    if ($new !== $confirm)     json_error('New passwords do not match.');

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($current, $user['password'])) json_error('Current password is incorrect.');

    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_DEFAULT), $user_id]);
    json_ok();
}

json_error('Unknown action');
