<?php
// features/admin/user_profile.php
// Admin only — view any user's profile data
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) json_error('Not authenticated', 401);
if (!is_admin())     json_error('Forbidden', 403);

$target_id = intval($_GET['user_id'] ?? 0);
if ($target_id < 1) json_error('Invalid user ID');

$stmt = $pdo->prepare("SELECT id, username, email, role, avatar_path, created_at FROM users WHERE id = ?");
$stmt->execute([$target_id]);
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
$statsStmt->execute([$target_id]);

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

echo json_encode([
    'user'          => $user,
    'stats'         => $statsStmt->fetch(),
    'recent_orders' => $recentStmt->fetchAll(),
]);
