
<?php
require_once 'config.php';
require_once 'users.php';
require_once 'functions.php';

if (!empty($_SESSION['username'])) {
    redirect('dashboard.php');
}

if (empty($_SESSION['username']) && !empty($_COOKIE['rememberme'])) {
    $u = verify_remember_token($_COOKIE['rememberme']);
    if ($u) {
        $_SESSION['username'] = $u;
        $_SESSION['display'] = $USERS[$u]['display'] ?? $u;
        session_regenerate_id(true);
        redirect('dashboard.php');
    } else {
        clear_remember_cookie();
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request (CSRF).';
    } else {
        $user = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        if (isset($USERS[$user]) && password_verify($pass, $USERS[$user]['password_hash'])) {
            $_SESSION['username'] = $user;
            $_SESSION['display'] = $USERS[$user]['display'];
            session_regenerate_id(true);

            if ($remember) {
                $token = create_remember_token($user);
                set_remember_cookie($token, time() + 60 * 60 * 24 * 30);
            }
            redirect('dashboard.php');
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
<h1>Login</h1>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <?php foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
    </div>
<?php endif; ?>

<form method="post" action="">
    <label>Username: <input name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label><input type="checkbox" name="remember"> Remember me</label><br>
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <button type="submit">Login</button>
</form>
</body>
</html>