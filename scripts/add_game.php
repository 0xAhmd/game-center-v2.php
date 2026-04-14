<?php
// scripts/add_game.php — supports both image URL and file upload
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
require_admin('../HTML/login.html');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../HTML/add.html');
    exit;
}

$title       = trim($_POST['title']       ?? '');
$description = trim($_POST['description'] ?? '');
$price       = floatval($_POST['price']   ?? 0);
$genre       = trim($_POST['genre']       ?? '');
$image_url   = trim($_POST['image_url']   ?? '');
$image_path  = null;

$errors = [];

if (strlen($title) < 1 || strlen($title) > 100) $errors[] = "Title is required (max 100 chars).";
if ($price < 0)                                  $errors[] = "Price cannot be negative.";

// ── Handle image upload ────────────────────────────────────────────────────
if (!empty($_FILES['image_file']['name'])) {
    $file      = $_FILES['image_file'];
    $allowed   = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize   = 5 * 1024 * 1024; // 5 MB

    if (!in_array($file['type'], $allowed)) {
        $errors[] = "Image must be JPG, PNG, or WEBP.";
    } elseif ($file['size'] > $maxSize) {
        $errors[] = "Image must be under 5 MB.";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Upload error code: " . $file['error'];
    } else {
        $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename  = uniqid('game_', true) . '.' . $ext;
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $image_path = 'uploads/' . $filename;
            $image_url  = ''; // prefer local path
        } else {
            $errors[] = "Failed to move uploaded file.";
        }
    }
}

if (!empty($errors)) {
    $msg = urlencode(implode(' | ', $errors));
    header("Location: ../HTML/add.html?error=$msg");
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO games (title, description, price, genre, image_url, image_path)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->execute([$title, $description, $price, $genre, $image_url, $image_path]);

header('Location: ../HTML/index.html?success=1');
exit;
