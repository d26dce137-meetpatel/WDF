<?php
require_once __DIR__ . '/includes/auth.php'; require_admin();
require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/audit.php';
audit_log($pdo, 'VIEW_REGISTRATIONS', 'Opened registrations page');
$rows = $pdo->query("SELECT r.registration_id, r.registration_date, s.name AS student_name, e.event_name
 FROM registrations r LEFT JOIN students s ON s.student_id=r.student_id
 LEFT JOIN events e ON e.event_id=r.event_id ORDER BY r.registration_id DESC")->fetchAll();
$pageTitle = 'Registrations'; require __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">All Registrations</h1><div class="card"><div class="table-responsive"><table class="table table-striped mb-0">
<thead><tr><th>ID</th><th>Student</th><th>Event</th><th>Date</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= (int)$r['registration_id'] ?></td><td><?= htmlspecialchars($r['student_name'] ?? 'Unknown') ?></td><td><?= htmlspecialchars($r['event_name'] ?? 'Unknown') ?></td><td><?= htmlspecialchars($r['registration_date'] ?? '') ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="4" class="text-center">No registrations found.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
