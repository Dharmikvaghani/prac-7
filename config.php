<?php
// config.php - secure session and helpers

function secure_session_start(): void {
    $session_name = 'sec_session_id';
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $httponly = true;

    ini_set('session.use_only_cookies', '1');
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => $cookieParams['path'] ?? '/',
        'domain'   => $cookieParams['domain'] ?? '',
        'secure'   => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax'
    ]);

    session_name($session_name);
    session_start();

    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

secure_session_start();

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function secure_session_destroy(): void {
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'] ?? '/', $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? false);
    session_destroy();
}