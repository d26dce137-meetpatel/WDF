
<?php
require_once 'config.php';
require_admin();

$q = trim((string)($_GET['q'] ?? ''));
$status = (string)($_GET['status'] ?? '');

$sql = 'SELECT * FROM events
        WHERE (
            event_name LIKE :q1
            OR event_category LIKE :q2
            OR venue LIKE :q3
        )';

$search = '%' . $q . '%';

$params = [
    ':q1' => $search,
    ':q2' => $search,
    ':q3' => $search
];

if (in_array($status, ['Open', 'Closed'], true)) {
    $sql .= ' AND status = :status';
    $params[':status'] = $status;
}

$sql .= ' ORDER BY event_date DESC, event_id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$total = (int)$pdo->query(
    'SELECT COUNT(*) FROM events'
)->fetchColumn();

$open = (int)$pdo->query(
    "SELECT COUNT(*) FROM events WHERE status = 'Open'"
)->fetchColumn();

$closed = $total - $open;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Events | Event Manager</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link href="style.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark app-nav">
    <div class="container">

        <a class="navbar-brand fw-bold" href="events.php">
            Event Manager
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50 small">
                Signed in as
                <?= e($_SESSION['admin_username'] ?? 'Admin') ?>
            </span>

            <a class="btn btn-outline-light btn-sm" href="logout.php">
                Logout
            </a>
        </div>

    </div>
</nav>

<main class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <p class="eyebrow mb-1">ADMIN PANEL</p>

            <h1 class="page-title mb-1">Events</h1>

            <p class="text-secondary mb-0">
                Create, update and manage event listings and posters.
            </p>
        </div>

        <a href="event_form.php" class="btn btn-primary">
            + Add Event
        </a>

    </div>

    <?php show_flash(); ?>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="stat-card">
                <span>Total events</span>
                <strong><?= $total ?></strong>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <span>Open events</span>
                <strong><?= $open ?></strong>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <span>Closed events</span>
                <strong><?= $closed ?></strong>
            </div>
        </div>

    </div>

    <form class="filter-bar row g-2 mb-3" method="get">

        <div class="col-md-7">
            <input
                type="text"
                class="form-control"
                name="q"
                value="<?= e($q) ?>"
                placeholder="Search event, category or venue"
            >
        </div>

        <div class="col-md-3">
            <select class="form-select" name="status">

                <option value="">All statuses</option>

                <option
                    value="Open"
                    <?= $status === 'Open' ? 'selected' : '' ?>
                >
                    Open
                </option>

                <option
                    value="Closed"
                    <?= $status === 'Closed' ? 'selected' : '' ?>
                >
                    Closed
                </option>

            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-dark">
                Filter
            </button>
        </div>

    </form>

    <div class="table-responsive panel">

        <table class="table align-middle mb-0">

            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Event</th>
                    <th>Date &amp; Venue</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($events)): ?>

                    <tr>
                        <td colspan="5" class="text-center py-5 text-secondary">
                            No events found. Add an event to get started.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($events as $event): ?>

                        <tr>

                            <td>
                                <?php
                                $posterFile = !empty($event['poster'])
                                    ? basename($event['poster'])
                                    : '';

                                if (
                                    $posterFile !== '' &&
                                    is_file(UPLOAD_DIR . $posterFile)
                                ):
                                ?>

                                    <img
                                        class="poster-thumb"
                                        src="uploads/<?= rawurlencode($posterFile) ?>"
                                        alt="Event poster"
                                    >

                                <?php else: ?>

                                    <div class="poster-placeholder">
                                        No poster
                                    </div>

                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    <?= e($event['event_name']) ?>
                                </div>

                                <div class="small text-secondary">
                                    <?= e($event['event_category']) ?>
                                </div>
                            </td>

                            <td>
                                <?= e($event['event_date']) ?>

                                <div class="small text-secondary">
                                    <?= e($event['venue']) ?>
                                </div>
                            </td>

                            <td>
                                <span class="badge rounded-pill
                                    <?= $event['status'] === 'Open'
                                        ? 'text-bg-success'
                                        : 'text-bg-secondary' ?>">

                                    <?= e($event['status']) ?>

                                </span>
                            </td>

                            <td class="text-end text-nowrap">

                                <a
                                    class="btn btn-sm btn-outline-primary"
                                    href="event_form.php?id=<?= (int)$event['event_id'] ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    class="d-inline"
                                    method="post"
                                    action="delete_event.php"
                                    onsubmit="return confirm('Delete this event? This cannot be undone.');"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= e(csrf_token()) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="event_id"
                                        value="<?= (int)$event['event_id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

    <p class="small text-secondary mt-3">
        Poster validation: JPG, PNG or WebP; maximum size 2 MB.
        Event changes are recorded in the audit log.
    </p>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
