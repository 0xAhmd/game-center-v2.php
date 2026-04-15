<?php
// scripts/upload_avatar.php — Profile picture upload
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (empty($_FILES['avatar']['name'])) {
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file    = $_FILES['avatar'];
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize = 2 * 1024 * 1024; // 2 MB

if (!in_array($file['type'], $allowed)) {
    echo json_encode(['error' => 'Image must be JPG, PNG, WEBP, or GIF.']);
    exit;
}
if ($file['size'] > $maxSize) {
    echo json_encode(['error' => 'Image must be under 2 MB.']);
    exit;
}
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'Upload error: ' . $file['error']]);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/avatars/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Remove old avatar if exists
$stmt = $pdo->prepare("SELECT avatar_path FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$existing = $stmt->fetchColumn();
if ($existing && file_exists(__DIR__ . '/../' . $existing)) {
    @unlink(__DIR__ . '/../' . $existing);
}

$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $user_id . '_' . uniqid() . '.' . $ext;

if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
    echo json_encode(['error' => 'Failed to save file.']);
    exit;
}

$avatar_path = 'uploads/avatars/' . $filename;
$pdo->prepare("UPDATE users SET avatar_path = ? WHERE id = ?")
    ->execute([$avatar_path, $user_id]);

echo json_encode(['success' => true, 'avatar_path' => $avatar_path]);
