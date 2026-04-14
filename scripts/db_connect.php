<?php
// scripts/db_connect.php
// PDO-based connection — used by all new scripts
// Legacy MySQLi $conn kept for backward compatibility with original scripts

$db   = "game_center";
$host = "localhost";
$user = "root";
$pass = "";

// ── PDO (used by all new auth / cart / order scripts) ──────────────────────
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed.']));
}

// ── MySQLi (kept for original get_games / add_game / etc.) ─────────────────
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("MySQLi Connection failed: " . mysqli_connect_error());
}
