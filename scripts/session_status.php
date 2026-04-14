<?php
// scripts/session_status.php — returns current auth state as JSON
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);

header('Content-Type: application/json');
echo json_encode([
    'logged_in' => is_logged_in(),
    'user_id'   => $_SESSION['user_id']  ?? null,
    'username'  => $_SESSION['username'] ?? null,
    'role'      => $_SESSION['role']     ?? null,
]);
