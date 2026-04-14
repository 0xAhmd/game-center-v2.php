<?php
// scripts/update_game.php
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }

$id          = intval($_POST['id']          ?? 0);
$title       = trim($_POST['title']         ?? '');
$genre       = trim($_POST['genre']         ?? '');
$description = trim($_POST['description']   ?? '');
$price       = floatval($_POST['price']     ?? 0);
$image_url   = trim($_POST['image_url']     ?? '');
$image_path  = null;

// ── Handle optional image re-upload ───────────────────────────────────────
if (!empty($_FILES['image_file']['name'])) {
    $file    = $_FILES['image_file'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;
    if (in_array($file['type'], $allowed) && $file['size'] <= $maxSize && $file['error'] === UPLOAD_ERR_OK) {
        $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename  = uniqid('game_', true) . '.' . $ext;
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $image_path = 'uploads/' . $filename;
            $image_url  = '';
        }
    }
}

if ($image_path) {
    $stmt = $pdo->prepare(
        "UPDATE games SET title=?, genre=?, description=?, price=?, image_url=?, image_path=? WHERE id=?"
    );
    $stmt->execute([$title, $genre, $description, $price, $image_url, $image_path, $id]);
} else {
    $stmt = $pdo->prepare(
        "UPDATE games SET title=?, genre=?, description=?, price=?, image_url=? WHERE id=?"
    );
    $stmt->execute([$title, $genre, $description, $price, $image_url, $id]);
}

echo "Game updated successfully!";
