<?php
require_once __DIR__ . '/includes/auth.php'; require_admin();
require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/audit.php';
audit_log($pdo, 'VIEW_AUDIT_LOGS', 'Opened audit logs page');
$rows = $pdo->query("SELECT a.audit_id, a.user_id, u.name AS user_name, a.action, a.details, a.ip_address, a.created_at
 FROM audit_logs a LEFT JOIN users u ON u.user_id=a.user_id ORDER BY a.audit_id DESC LIMIT 200")->fetchAll();
$pageTitle = 'Audit Logs'; require __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">Audit Logs</h1><p class="text-secondary">Latest 200 recorded actions.</p>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
<thead class="table-light"><tr><th>ID</th><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>Time</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= (int)$r['audit_id'] ?></td><td><?= htmlspecialchars($r['user_name'] ?? ('User #' . $r['user_id'])) ?></td><td><span class="badge text-bg-secondary"><?= htmlspecialchars($r['action']) ?></span></td><td><?= htmlspecialchars($r['details'] ?? '') ?></td><td><?= htmlspecialchars($r['ip_address'] ?? '') ?></td><td><?= htmlspecialchars($r['created_at']) ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="6" class="text-center">No audit entries yet.</td></tr><?php endif; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
