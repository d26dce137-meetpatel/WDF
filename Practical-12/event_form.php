<?php
require_once 'config.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$editing = $id > 0;
$event = ['event_name'=>'','event_category'=>'General','event_date'=>'','venue'=>'','description'=>'','poster'=>null,'status'=>'Open'];
if ($editing) {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash('danger', 'Event not found.');
        header('Location: events.php');
        exit;
    }
    $event = $found;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $event['event_name'] = trim((string)($_POST['event_name'] ?? ''));
    $event['event_category'] = trim((string)($_POST['event_category'] ?? ''));
    $event['event_date'] = trim((string)($_POST['event_date'] ?? ''));
    $event['venue'] = trim((string)($_POST['venue'] ?? ''));
    $event['description'] = trim((string)($_POST['description'] ?? ''));
    $event['status'] = (string)($_POST['status'] ?? 'Open');

    if ($event['event_name'] === '' || mb_strlen($event['event_name']) > 150) $errors[] = 'Event name is required (maximum 150 characters).';
    if ($event['event_category'] === '' || mb_strlen($event['event_category']) > 80) $errors[] = 'Category is required (maximum 80 characters).';
    $date = DateTime::createFromFormat('!Y-m-d', $event['event_date']);
    if (!$date || $date->format('Y-m-d') !== $event['event_date']) $errors[] = 'Enter a valid event date.';
    if ($event['venue'] === '' || mb_strlen($event['venue']) > 180) $errors[] = 'Venue is required (maximum 180 characters).';
    if (!in_array($event['status'], ['Open','Closed'], true)) $errors[] = 'Choose a valid event status.';

    $newPoster = null;
    $removePoster = isset($_POST['remove_poster']) && $_POST['remove_poster'] === '1';
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Poster upload failed. Please try again.';
        } elseif ($file['size'] > MAX_POSTER_SIZE) {
            $errors[] = 'Poster must be 2 MB or smaller.';
        } elseif (!is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'Invalid uploaded file.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!isset($allowed[$mime]) || $extension !== $allowed[$mime]) {
                $errors[] = 'Invalid poster type. Upload a matching JPG, PNG or WebP image.';
            } else {
                $newPoster = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
            }
        }
    }

    if (!$errors) {
        $oldPoster = $event['poster'] ?? null;
        $posterValue = $newPoster ?? ($removePoster ? null : $oldPoster);
        if ($newPoster !== null && !move_uploaded_file($_FILES['poster']['tmp_name'], UPLOAD_DIR . $newPoster)) {
            $errors[] = 'Could not save the uploaded poster. Check folder permissions.';
        } else {
            if ($editing) {
                $stmt = $pdo->prepare('UPDATE events SET event_name=?, event_category=?, event_date=?, venue=?, description=?, poster=?, status=? WHERE event_id=?');
                $stmt->execute([$event['event_name'], $event['event_category'], $event['event_date'], $event['venue'], $event['description'] ?: null, $posterValue, $event['status'], $id]);
                log_event($pdo, $id, 'UPDATE', 'Updated event: ' . $event['event_name']);
                flash('success', 'Event updated successfully.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO events (event_name,event_category,event_date,venue,description,poster,status) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$event['event_name'], $event['event_category'], $event['event_date'], $event['venue'], $event['description'] ?: null, $posterValue, $event['status']]);
                $id = (int)$pdo->lastInsertId();
                log_event($pdo, $id, 'CREATE', 'Created event: ' . $event['event_name']);
                flash('success', 'Event added successfully.');
            }
            if (($newPoster !== null || $removePoster) && $oldPoster && basename($oldPoster) === $oldPoster && is_file(UPLOAD_DIR . $oldPoster)) {
                @unlink(UPLOAD_DIR . $oldPoster);
            }
            header('Location: events.php');
            exit;
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $editing?'Edit':'Add' ?> Event</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="style.css" rel="stylesheet"></head>
<body><nav class="navbar navbar-dark app-nav"><div class="container"><a class="navbar-brand fw-bold" href="events.php">Event Manager</a><a class="btn btn-outline-light btn-sm" href="events.php">← Back to events</a></div></nav>
<main class="container py-4 form-wrap"><div class="mb-4"><p class="eyebrow mb-1">EVENT MANAGEMENT</p><h1 class="page-title"><?= $editing?'Edit event':'Add new event' ?></h1><p class="text-secondary">Enter event information and optionally attach a poster.</p></div>
<?php if ($errors): ?><div class="alert alert-danger"><strong>Please correct the following:</strong><ul class="mb-0 mt-2"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="panel p-4">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="row g-3">
<div class="col-md-8"><label class="form-label">Event name *</label><input class="form-control" name="event_name" maxlength="150" required value="<?= e($event['event_name']) ?>"></div>
<div class="col-md-4"><label class="form-label">Category *</label><input class="form-control" name="event_category" maxlength="80" required value="<?= e($event['event_category']) ?>" placeholder="Technology, Cultural..."></div>
<div class="col-md-6"><label class="form-label">Event date *</label><input class="form-control" type="date" name="event_date" required value="<?= e($event['event_date']) ?>"></div>
<div class="col-md-6"><label class="form-label">Venue *</label><input class="form-control" name="venue" maxlength="180" required value="<?= e($event['venue']) ?>"></div>
<div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4" placeholder="Brief event details"><?= e($event['description']) ?></textarea></div>
<div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status"><option value="Open" <?= $event['status']==='Open'?'selected':'' ?>>Open</option><option value="Closed" <?= $event['status']==='Closed'?'selected':'' ?>>Closed</option></select></div>
<div class="col-md-6"><label class="form-label">Poster (optional)</label><input class="form-control" type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><div class="form-text">Allowed: JPG, PNG, WebP. Maximum size: 2 MB.</div></div>
<?php if ($editing && $event['poster'] && is_file(UPLOAD_DIR . basename($event['poster']))): ?><div class="col-12"><div class="d-flex align-items-center gap-3"><img class="poster-preview" src="uploads/<?= rawurlencode(basename($event['poster'])) ?>" alt="Current poster"><div><div class="fw-semibold">Current poster</div><label class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_poster" value="1"><span class="form-check-label">Remove current poster</span></label></div></div></div><?php endif; ?>
<div class="col-12 d-flex gap-2 pt-2"><button class="btn btn-primary" type="submit"><?= $editing?'Save changes':'Create event' ?></button><a class="btn btn-light border" href="events.php">Cancel</a></div>
</div></form></main></body></html>
