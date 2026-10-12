<?php
if (!isset($pageTitle)) $pageTitle = 'StudentHub Admin';
$role = strtolower((string)($_SESSION['role'] ?? 'student'));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
 <div class="container"><a class="navbar-brand fw-bold" href="index.php">StudentHub Admin</a>
 <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
 <div class="collapse navbar-collapse" id="mainNav">
  <ul class="navbar-nav me-auto">
   <?php if ($role === 'admin'): ?>
    <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
    <li class="nav-item"><a class="nav-link" href="registrations.php">Registrations</a></li>
    <li class="nav-item"><a class="nav-link" href="audit_logs.php">Audit Logs</a></li>
   <?php else: ?>
    <li class="nav-item"><a class="nav-link" href="student.php">Student Home</a></li>
   <?php endif; ?>
  </ul>
  <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?> (<?= htmlspecialchars($role) ?>)</span>
  <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
 </div></div>
</nav>
<main class="container py-4">
