<?php
// scripts/session_status.php — returns current auth state as JSON
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);

// Fetch avatar_path from DB if logged in
$avatar_path = null;
if (is_logged_in()) {
    $stmt = $pdo->prepare("SELECT avatar_path FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    $avatar_path = $row['avatar_path'] ?? null;
}

header('Content-Type: application/json');
echo json_encode([
    'logged_in'   => is_logged_in(),
    'user_id'     => $_SESSION['user_id']  ?? null,
    'username'    => $_SESSION['username'] ?? null,
    'role'        => $_SESSION['role']     ?? null,
    'avatar_path' => $avatar_path,
]);
