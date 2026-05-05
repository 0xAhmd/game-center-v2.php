<?php
require_once '../shared/auth_helpers.php';

$_SESSION = [];
session_destroy();

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header('Location: ../../HTML/login.html');
exit;
