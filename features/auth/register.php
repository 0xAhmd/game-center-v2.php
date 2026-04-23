<?php
// features/auth/register.php
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../HTML/register.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$confirm  = $_POST['confirm']       ?? '';

$errors = [];

if (strlen($username) < 3 || strlen($username) > 50) $errors[] = "Username must be 3–50 characters.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))       $errors[] = "Invalid email address.";
if (strlen($password) < 6)                            $errors[] = "Password must be at least 6 characters.";
if ($password !== $confirm)                           $errors[] = "Passwords do not match.";

if (!empty($errors)) {
    header("Location: ../../HTML/register.html?error=" . urlencode(implode(' | ', $errors)));
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$email, $username]);
if ($stmt->fetch()) {
    header("Location: ../../HTML/register.html?error=" . urlencode("Email or username already taken."));
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')")
    ->execute([$username, $email, $hash]);

header("Location: ../../HTML/login.html?success=" . urlencode("Account created! Please log in."));
exit;
