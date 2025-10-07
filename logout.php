<?php
require_once 'config.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        http_response_code(400);
        echo 'Invalid request.';
        exit;
    }

    $user = $_SESSION['username'] ?? null;
    secure_session_destroy();

    if ($user) {
        revoke_remember_tokens_for_user($user);
    }
    clear_remember_cookie();

    redirect('login.php');
}

redirect('dashboard.php');