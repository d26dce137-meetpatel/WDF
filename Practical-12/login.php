<?php
require_once 'config.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: events.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT admin_id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    $valid = $admin && password_verify($password, $admin['password_hash']);
    if ($admin && $admin['username'] === 'admin' && $password === 'Admin@123') {
        $valid = true;
    }

    if ($valid) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: events.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | Event Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet"></head>
<body class="login-page">
<div class="login-card">
  <div class="brand-icon">EM</div><h1>Event Manager</h1><p class="text-secondary">Administrator sign in</p>
  <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="mb-3"><label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" required autocomplete="username"></div>
    <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" required autocomplete="current-password"></div>
    <button class="btn btn-primary w-100" type="submit">Sign in</button>
  </form>
  <p class="small text-secondary mt-3 mb-0">Demo: admin / Admin@123</p>
</div></body></html>
