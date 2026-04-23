<?php
// features/reviews/reviews.php
// Actions: add_review, get_reviews_by_game
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// GET: all reviews for a game (public)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_reviews_by_game') {
    $game_id = intval($_GET['game_id'] ?? 0);
    if ($game_id < 1) json_error('Invalid game ID');

    $stmt = $pdo->prepare("
        SELECT r.id, r.rating, r.comment, r.created_at,
               u.username, u.avatar_path
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.game_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$game_id]);
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST: add review (must own game)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_review') {
    if (!is_logged_in()) json_error('Not authenticated', 401);

    $user_id = $_SESSION['user_id'];
    $game_id = intval($_POST['game_id'] ?? 0);
    $rating  = intval($_POST['rating']  ?? 0);
    $comment = trim($_POST['comment']   ?? '');

    if ($game_id < 1)               json_error('Invalid game ID');
    if ($rating < 1 || $rating > 5) json_error('Rating must be between 1 and 5');
    if (strlen($comment) > 1000)    json_error('Comment too long (max 1000 chars)');

    // Check ownership
    $owns = $pdo->prepare("SELECT id FROM user_library WHERE user_id = ? AND game_id = ?");
    $owns->execute([$user_id, $game_id]);
    if (!$owns->fetch()) json_error('You must own this game to review it', 403);

    // Upsert — one review per user per game
    $stmt = $pdo->prepare("
        INSERT INTO reviews (user_id, game_id, rating, comment)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
    ");
    $stmt->execute([$user_id, $game_id, $rating, $comment]);
    json_ok(['message' => 'Review saved']);
}

json_error('Unknown action');