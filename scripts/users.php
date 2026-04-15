<?php
// scripts/users.php — Admin user management API
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ── GET all users ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $stmt = $pdo->query("SELECT id, username, email, role, avatar_path, created_at FROM users ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

// ── POST: update role ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_role') {
    $uid  = intval($_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';
    if (!in_array($role, ['admin', 'user'])) {
        http_response_code(400); echo json_encode(['error' => 'Invalid role']); exit;
    }
    // Prevent self-demotion
    if ($uid === (int)$_SESSION['user_id'] && $role !== 'admin') {
        http_response_code(400); echo json_encode(['error' => 'Cannot demote yourself']); exit;
    }
    $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$role, $uid]);
    echo json_encode(['success' => true]);
    exit;
}

// ── POST: delete user ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $uid = intval($_POST['user_id'] ?? 0);
    if ($uid === (int)$_SESSION['user_id']) {
        http_response_code(400); echo json_encode(['error' => 'Cannot delete yourself']); exit;
    }
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
