<?php
// features/admin/users.php
// Admin only — list users, update role, delete user
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_admin()) json_error('Forbidden', 403);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// GET: list all users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $stmt = $pdo->query("SELECT id, username, email, role, avatar_path, created_at FROM users ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST: update role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_role') {
    $uid  = intval($_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';
    if (!in_array($role, ['admin', 'user'])) json_error('Invalid role');
    if ($uid === (int)$_SESSION['user_id'] && $role !== 'admin') json_error('Cannot demote yourself');
    $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $uid]);
    json_ok();
}

// POST: delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $uid = intval($_POST['user_id'] ?? 0);
    if ($uid === (int)$_SESSION['user_id']) json_error('Cannot delete yourself');
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
    json_ok();
}

json_error('Unknown action');
