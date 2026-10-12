<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/audit.php';

$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalRegistrations = (int)$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

$stmt = $pdo->query("SELECT r.*, s.name AS student_name, e.event_name
    FROM registrations r
    LEFT JOIN students s ON s.student_id = r.student_id
    LEFT JOIN events e ON e.event_id = r.event_id
    ORDER BY r.registration_id DESC LIMIT 8");
$latest = $stmt->fetchAll();

$pageTitle = 'Admin Dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div><h1 class="h3 mb-1">Admin Dashboard</h1><p class="text-secondary mb-0">Overview of StudentHub activity</p></div>
  <span class="badge text-bg-success">Admin access</span>
</div>
<div class="row g-3 mb-4">
  <?php foreach ([
    ['Total Users', $totalUsers, 'bi-people'],
    ['Students', $totalStudents, 'bi-mortarboard'],
    ['Events', $totalEvents, 'bi-calendar-event'],
    ['Registrations', $totalRegistrations, 'bi-journal-check']
  ] as [$label, $value, $icon]): ?>
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="card stat-card h-100"><div class="card-body d-flex justify-content-between align-items-center">
      <div><div class="text-secondary small"><?= htmlspecialchars($label) ?></div><div class="display-6 fw-semibold"><?= $value ?></div></div>
      <i class="bi <?= htmlspecialchars($icon) ?> fs-1 text-primary"></i>
    </div></div>
  </div>
  <?php endforeach; ?>
</div>
<div class="card shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h2 class="h5 mb-0">Latest Registrations</h2><a href="registrations.php" class="btn btn-sm btn-outline-primary">View all</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>#</th><th>Student</th><th>Event</th><th>Date</th></tr></thead>
      <tbody>
      <?php if (!$latest): ?><tr><td colspan="4" class="text-center text-secondary py-4">No registrations found.</td></tr>
      <?php else: foreach ($latest as $row): ?>
        <tr><td><?= (int)$row['registration_id'] ?></td><td><?= htmlspecialchars($row['student_name'] ?? 'Unknown student') ?></td>
        <td><?= htmlspecialchars($row['event_name'] ?? 'Unknown event') ?></td>
        <td><?= htmlspecialchars($row['registration_date'] ?? '') ?></td></tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
