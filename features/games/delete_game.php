<?php
// features/games/delete_game.php
// Admin only
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
require_admin();

$id = intval($_GET['id'] ?? 0);
if ($id < 1) {
    echo "No ID provided.";
    exit;
}

$pdo->prepare("DELETE FROM games WHERE id = ?")->execute([$id]);
echo "Deleted Successfully";
