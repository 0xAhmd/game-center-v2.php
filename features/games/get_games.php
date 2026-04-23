<?php
// features/games/get_games.php
// Public endpoint — returns all games as JSON
require_once '../shared/db.php';

header('Content-Type: application/json; charset=utf-8');

$result = mysqli_query($conn, "SELECT * FROM games ORDER BY id DESC");

$games = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Prefer uploaded local file, fall back to URL
        $row['resolved_image'] = !empty($row['image_path'])
            ? '../../' . $row['image_path']
            : $row['image_url'];
        $games[] = $row;
    }
    echo json_encode($games);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn)]);
}
