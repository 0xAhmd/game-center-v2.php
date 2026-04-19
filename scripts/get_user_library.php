<?php
// scripts/get_user_library.php — Return all games owned by the logged-in user
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT
        ul.id            AS library_id,
        ul.purchase_date,
        g.id             AS game_id,
        g.title,
        g.genre,
        g.price,
        g.description,
        g.image_url,
        g.image_path
    FROM user_library ul
    JOIN games g ON ul.game_id = g.id
    WHERE ul.user_id = ?
    ORDER BY ul.purchase_date DESC
");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll();

// Resolve image paths (same logic as get_games.php)
foreach ($rows as &$row) {
    $row['resolved_image'] = !empty($row['image_path'])
        ? '../' . $row['image_path']
        : $row['image_url'];
}

echo json_encode($rows);
