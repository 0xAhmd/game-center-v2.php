<?php
// features/profile/get_user_library.php
// Returns all games owned by the logged-in user (for library page)
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) json_error('Not authenticated', 401);

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
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();

foreach ($rows as &$row) {
    $row['resolved_image'] = !empty($row['image_path'])
        ? '../../' . $row['image_path']
        : $row['image_url'];
}

echo json_encode($rows);
