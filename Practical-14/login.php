<?php
require_once __DIR__ . '/includes/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT user_id, name, email, password, role, status FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && (!isset($user['status']) || $user['status'] === 'active') && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        require_once __DIR__ . '/includes/audit.php';
        audit_log($pdo, 'LOGIN', 'Successful login');
        header('Location: ' . (strtolower($user['role']) === 'admin' ? 'index.php' : 'student.php'));
        exit;
    }
    $error = 'Invalid credentials or inactive account.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - StudentHub</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="assets/style.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container min-vh-100 d-flex align-items-center justify-content-center"><div class="card shadow-sm login-card w-100"><div class="card-body p-4 p-md-5">
<h1 class="h3 mb-1">StudentHub Login</h1><p class="text-secondary mb-4">Sign in with your account</p>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post"><div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
<button class="btn btn-primary w-100">Login</button></form>
<p class="small text-secondary mt-3 mb-0">Create an admin account in the database using a PHP password hash.</p>
</div></div></div></body></html>
