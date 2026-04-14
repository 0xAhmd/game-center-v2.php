<?php
// scripts/register.php
require_once 'db_connect.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/register.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$confirm  = $_POST['confirm']       ?? '';

$errors = [];

// ── Validation ─────────────────────────────────────────────────────────────
if (strlen($username) < 3 || strlen($username) > 50) {
    $errors[] = "Username must be between 3 and 50 characters.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}
if ($password !== $confirm) {
    $errors[] = "Passwords do not match.";
}

if (!empty($errors)) {
    $msg = urlencode(implode(' | ', $errors));
    header("Location: ../HTML/register.html?error=$msg");
    exit;
}

// ── Check uniqueness ───────────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
if ($stmt->fetch()) {
    header("Location: ../HTML/register.html?error=" . urlencode("Email or username already taken."));
    exit;
}

// ── Insert ─────────────────────────────────────────────────────────────────
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
$stmt->execute([$username, $email, $hash]);

header("Location: ../HTML/login.html?success=" . urlencode("Account created! Please log in."));
exit;
