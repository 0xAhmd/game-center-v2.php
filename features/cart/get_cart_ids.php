<?php
// features/cart/get_cart_ids.php
// Returns array of game IDs in the user's cart — used by store & game detail pages
require_once '../shared/db.php';
require_once '../shared/auth_helpers.php';

auto_login_from_cookie($pdo);
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode([]); exit; }

$stmt = $pdo->prepare("SELECT game_id FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
