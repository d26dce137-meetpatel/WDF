<?php
require 'config.php';
require 'functions.php';

$perPage = 8;
$page = max(1, (int)($_GET['page'] ?? 1));

[$where, $types, $params] = build_filters($_GET);

// 1) Count total matching rows (for pagination)
$stmt = $conn->prepare("SELECT COUNT(*) FROM students $where");
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_row()[0];

$pages  = max(1, (int)ceil($total / $perPage));
$page   = min($page, $pages);
$offset = ($page - 1) * $perPage;

// 2) Fetch current page
$stmt = $conn->prepare("SELECT * FROM students $where ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param($types . 'ii', ...array_merge($params, [$perPage, $offset]));
$stmt->execute();
$rows = $stmt->get_result();

$q      = trim($_GET['q'] ?? '');
$course = $_GET['course'] ?? '';
$status = $_GET['status'] ?? 'active';

require 'header.php';
?>

<form method="GET" class="filters">
    <input type="text" name="q" placeholder="Search name / email / course" value="<?= e($q) ?>">

    <select name="course">
        <option value="">All Courses</option>
        <?php foreach (COURSES as $c): ?>
            <option value="<?= e($c) ?>" <?= $course === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="status">
        <option value="active"  <?= $status === 'active'  ? 'selected' : '' ?>>Active</option>
        <option value="deleted" <?= $status === 'deleted' ? 'selected' : '' ?>>Deleted</option>
        <option value="all"     <?= $status === 'all'     ? 'selected' : '' ?>>All</option>
    </select>

    <button type="submit">Search</button>
    <a href="index.php" class="btn gray">Reset</a>
    <a href="export_csv.php?<?= e(http_build_query($_GET)) ?>" class="btn green">Export CSV</a>
</form>

<p><b><?= $total ?></b> record(s) found.</p>

<table>
    <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Phone</th>
        <th>Course</th><th>Status</th><th>Actions</th>
    </tr>

    <?php if ($total === 0): ?>
        <tr><td colspan="7" style="text-align:center">No students found.</td></tr>
    <?php endif; ?>

    <?php while ($r = $rows->fetch_assoc()): ?>
    <tr class="<?= $r['status'] === 'deleted' ? 'deleted' : '' ?>">
        <td><?= (int)$r['id'] ?></td>
        <td><?= e($r['name']) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['phone']) ?></td>
        <td><?= e($r['course']) ?></td>
        <td><?= e($r['status']) ?></td>
        <td class="actions">
            <?php if ($r['status'] === 'active'): ?>
                <a href="edit.php?id=<?= (int)$r['id'] ?>" class="btn">Edit</a>

                <form method="POST" action="delete.php"
                      onsubmit="return confirm('Move <?= e(addslashes($r['name'])) ?> to deleted list?');">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="action" value="soft">
                    <button class="red">Delete</button>
                </form>
            <?php else: ?>
                <form method="POST" action="delete.php"
                      onsubmit="return confirm('Restore this student?');">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="action" value="restore">
                    <button class="green">Restore</button>
                </form>

                <form method="POST" action="delete.php"
                      onsubmit="return confirm('PERMANENTLY delete this record? This cannot be undone!');">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <input type="hidden" name="action" value="permanent">
                    <button class="red">Delete Forever</button>
                </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php if ($pages > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?<?= e(http_build_query(array_merge($_GET, ['page' => $i]))) ?>"
           class="<?= $i === $page ? 'current' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php require 'footer.php'; ?>