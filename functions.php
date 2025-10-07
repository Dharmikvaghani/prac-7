<?php
// functions.php - remember-me token helpers (uses token_store.json)

function load_token_store(): array {
    $path = __DIR__ . '/token_store.json';
    if (!file_exists($path)) {
        file_put_contents($path, json_encode(new stdClass(), JSON_PRETTY_PRINT), LOCK_EX);
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function save_token_store(array $arr): void {
    $path = __DIR__ . '/token_store.json';
    file_put_contents($path, json_encode($arr, JSON_PRETTY_PRINT), LOCK_EX);
}

function create_remember_token(string $username): string {
    $token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $token);
    $store = load_token_store();
    $store[$token_hash] = [
        'username' => $username,
        'expires'  => time() + 60 * 60 * 24 * 30
    ];
    save_token_store($store);
    return $token;
}

function verify_remember_token(?string $token) {
    if (empty($token)) return false;
    $token_hash = hash('sha256', $token);
    $store = load_token_store();
    if (isset($store[$token_hash])) {
        $entry = $store[$token_hash];
        if ($entry['expires'] >= time()) {
            return $entry['username'];
        }
        unset($store[$token_hash]);
        save_token_store($store);
    }
    return false;
}

function revoke_remember_tokens_for_user(string $username): void {
    $store = load_token_store();
    foreach ($store as $k => $v) {
        if (isset($v['username']) && $v['username'] === $username) {
            unset($store[$k]);
        }
    }
    save_token_store($store);
}

function set_remember_cookie(string $token, int $expires): void {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('rememberme', $token, [
        'expires'  => $expires,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

function clear_remember_cookie(): void {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie('rememberme', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}