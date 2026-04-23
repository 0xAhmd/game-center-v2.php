<?php
// features/auth/session_status.php
// Returns the current login state as JSON — called by auth_ui.js on every page
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);

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
