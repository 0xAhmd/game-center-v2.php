<?php
// features/shared/auth_helpers.php
// Session start + all auth utility functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auto_login_from_cookie(PDO $pdo): void {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        [$uid, $hash] = array_pad(explode(':', $token, 2), 2, '');
        $stmt = $pdo->prepare("SELECT id, username, role, password FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        $user = $stmt->fetch();
        if ($user && hash_equals(hash('sha256', $user['password'] . $uid), $hash)) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
        }
    }
}

function require_login(string $redirect = '../../HTML/login.html'): void {
    if (empty($_SESSION['user_id'])) {
        header("Location: $redirect");
        exit;
    }
}

function require_admin(string $redirect = '../../HTML/login.html'): void {
    require_login($redirect);
    if ($_SESSION['role'] !== 'admin') {
        header("Location: $redirect");
        exit;
    }
}

function is_admin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function json_error(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

function json_ok(array $data = []): void {
    echo json_encode(array_merge(['success' => true], $data));
    exit;
}
