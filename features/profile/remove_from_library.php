<?php
// features/profile/remove_from_library.php
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in())                        json_error('Not authenticated', 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST')  json_error('Method not allowed', 405);

$game_id = intval($_POST['game_id'] ?? 0);
if ($game_id < 1) json_error('Invalid game ID');

$stmt = $pdo->prepare("DELETE FROM user_library WHERE user_id = ? AND game_id = ?");
$stmt->execute([$_SESSION['user_id'], $game_id]);

if ($stmt->rowCount() === 0) json_error('Game not found in your library');

json_ok(['game_id' => $game_id]);
