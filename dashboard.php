<?php
require_once 'config.php';
require_once 'users.php';
require_once 'functions.php';

if (empty($_SESSION['username'])) {
    if (!empty($_COOKIE['rememberme'])) {
        $u = verify_remember_token($_COOKIE['rememberme']);
        if ($u) {
            $_SESSION['username'] = $u;
            $_SESSION['display'] = $USERS[$u]['display'] ?? $u;
            session_regenerate_id(true);
        }
    }
}

if (empty($_SESSION['username'])) {
    redirect('login.php');
}

$user = $_SESSION['username'];
$display = $_SESSION['display'] ?? $user;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
</head>
<body>
<h1>Welcome <?php echo htmlspecialchars($display); ?></h1>
<p>Your username: <?php echo htmlspecialchars($user); ?></p>

<form method="post" action="logout.php">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <button type="submit">Logout</button>
</form>
</body>
</html>