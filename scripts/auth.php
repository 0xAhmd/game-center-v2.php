<?php
// scripts/auth.php  — Authentication & Authorization helpers

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Auto-login via "Remember Me" cookie ────────────────────────────────────
function auto_login_from_cookie(PDO $pdo): void {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        // token format: user_id:hash
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

// ── Require login — redirect to login page if not authenticated ────────────
function require_login(string $redirect = '../HTML/login.html'): void {
    if (empty($_SESSION['user_id'])) {
        header("Location: $redirect");
        exit;
    }
}

// ── Require admin role ─────────────────────────────────────────────────────
function require_admin(string $redirect = '../HTML/index.html'): void {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: $redirect");
        exit;
    }
}

// ── Helper: is current user admin? ────────────────────────────────────────
function is_admin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// ── Helper: is user logged in? ────────────────────────────────────────────
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

// ── Sanitize scalar input ─────────────────────────────────────────────────
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}
