<?php
// scripts/add_to_library.php — Add a game to the logged-in user's library
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

// Verify game exists
$check = $pdo->prepare("SELECT id, title FROM games WHERE id = ?");
$check->execute([$game_id]);
$game = $check->fetch();

if (!$game) {
    http_response_code(404);
    echo json_encode(['error' => 'Game not found']);
    exit;
}

// Check if already owned
$owns = $pdo->prepare("SELECT id FROM user_library WHERE user_id = ? AND game_id = ?");
$owns->execute([$user_id, $game_id]);
if ($owns->fetch()) {
    echo json_encode(['error' => 'already_owned', 'message' => 'You already own this game.']);
    exit;
}

// Insert
$stmt = $pdo->prepare("INSERT INTO user_library (user_id, game_id) VALUES (?, ?)");
$stmt->execute([$user_id, $game_id]);

echo json_encode([
    'success' => true,
    'message' => "'{$game['title']}' added to your library!",
    'game_id' => $game_id,
]);
