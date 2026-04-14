<?php
// scripts/delete_game.php
require_once 'db_connect.php';
require_once 'auth.php';

auto_login_from_cookie($pdo);
require_admin();

if (isset($_GET['id'])) {
    $id   = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM games WHERE id = ?");
    $stmt->execute([$id]);
    echo "Deleted Successfully";
} else {
    echo "No ID provided.";
}
