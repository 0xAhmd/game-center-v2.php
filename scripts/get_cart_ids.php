<?php
// scripts/get_cart_ids.php — Returns an array of game IDs currently in the logged-in user's cart
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT game_id FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
