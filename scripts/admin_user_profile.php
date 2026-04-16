<?php
// scripts/admin_user_profile.php — Admin: view any user's profile data (JSON)
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

// Admin-only endpoint
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$target_id = intval($_GET['user_id'] ?? 0);
if ($target_id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

// Fetch user
$stmt = $pdo->prepare("SELECT id, username, email, role, avatar_path, created_at FROM users WHERE id = ?");
$stmt->execute([$target_id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Order stats
$statsStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(total_price), 0) AS total_spent,
        COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) AS completed_orders,
        COALESCE(SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END), 0) AS pending_orders
    FROM orders WHERE user_id = ?
");
$statsStmt->execute([$target_id]);
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
    LIMIT 5
");
$recentStmt->execute([$target_id]);
$recentOrders = $recentStmt->fetchAll();

echo json_encode([
    'user'          => $user,
    'stats'         => $stats,
    'recent_orders' => $recentOrders,
]);
