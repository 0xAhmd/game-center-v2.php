<?php
// scripts/logout.php
require_once 'auth.php';

$_SESSION = [];
session_destroy();

// Clear remember-me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header('Location: ../HTML/login.html');
exit;
