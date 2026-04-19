<?php
// scripts/remove_from_library.php — Remove a game from the logged-in user's library
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

$user_id = (int) $_SESSION['user_id'];
$game_id = intval($_POST['game_id'] ?? 0);

if ($game_id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid game ID']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM user_library WHERE user_id = ? AND game_id = ?");
$stmt->execute([$user_id, $game_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['error' => 'Game not found in your library']);
    exit;
}

echo json_encode(['success' => true, 'game_id' => $game_id]);
