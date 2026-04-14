<?php
// scripts/get_games.php
require_once 'db_connect.php';
header('Content-Type: application/json; charset=utf-8');

$sql    = "SELECT * FROM games ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$games = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Resolve image: prefer uploaded file, fallback to URL
        if (!empty($row['image_path'])) {
            $row['resolved_image'] = '../' . $row['image_path'];
        } else {
            $row['resolved_image'] = $row['image_url'];
        }
        $games[] = $row;
    }
    echo json_encode($games);
} else {
    echo json_encode(["error" => "Query failed: " . mysqli_error($conn)]);
}
