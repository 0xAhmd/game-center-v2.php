<?php
// features/auth/login.php
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../HTML/login.html');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$remember = !empty($_POST['remember']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../HTML/login.html?error=" . urlencode("Invalid email."));
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    header("Location: ../../HTML/login.html?error=" . urlencode("Invalid email or password."));
    exit;
}

$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];
session_regenerate_id(true);

if ($remember) {
    $token = $user['id'] . ':' . hash('sha256', $user['password'] . $user['id']);
    setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
}

header($user['role'] === 'admin'
    ? 'Location: ../../HTML/admin.html'
    : 'Location: ../../HTML/index.html');
exit;
