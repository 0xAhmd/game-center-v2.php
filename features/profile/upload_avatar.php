<?php
// features/profile/upload_avatar.php
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in())                              json_error('Not authenticated', 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST')        json_error('Method not allowed', 405);
if (empty($_FILES['avatar']['name']))             json_error('No file uploaded');

$user_id = $_SESSION['user_id'];
$file    = $_FILES['avatar'];
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSize = 2 * 1024 * 1024;

if (!in_array($file['type'], $allowed))           json_error('Image must be JPG, PNG, WEBP, or GIF.');
if ($file['size'] > $maxSize)                     json_error('Image must be under 2 MB.');
if ($file['error'] !== UPLOAD_ERR_OK)             json_error('Upload error: ' . $file['error']);

$uploadDir = __DIR__ . '/../../uploads/avatars/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

// Delete old avatar if one exists
$stmt = $pdo->prepare("SELECT avatar_path FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$existing = $stmt->fetchColumn();
if ($existing && file_exists(__DIR__ . '/../../' . $existing)) {
    @unlink(__DIR__ . '/../../' . $existing);
}

$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $user_id . '_' . uniqid() . '.' . $ext;

if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
    json_error('Failed to save file.');
}

$avatar_path = 'uploads/avatars/' . $filename;
$pdo->prepare("UPDATE users SET avatar_path = ? WHERE id = ?")->execute([$avatar_path, $user_id]);

json_ok(['avatar_path' => $avatar_path]);
