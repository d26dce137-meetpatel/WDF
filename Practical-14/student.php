<?php
require_once __DIR__ . '/includes/auth.php'; require_login();
if (strtolower((string)($_SESSION['role'] ?? '')) === 'admin') { header('Location: index.php'); exit; }
require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/audit.php';
audit_log($pdo, 'VIEW_STUDENT_HOME', 'Opened student home');
$pageTitle = 'Student Home'; require __DIR__ . '/includes/header.php';
?>
<div class="card"><div class="card-body"><h1 class="h3">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?></h1>
<p class="mb-0">You are signed in with the <strong>student</strong> role. Admin dashboard links are hidden and admin pages are blocked.</p></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
