<?php
require_once 'config.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}
verify_csrf();
$id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT poster, event_name FROM events WHERE event_id = ?');
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) {
    flash('danger', 'Event not found or already deleted.');
    header('Location: events.php');
    exit;
}
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
    $stmt->execute([$id]);
    log_event($pdo, (int)$id, 'DELETE', 'Deleted event: ' . $event['event_name']);
    $pdo->commit();
    if ($event['poster'] && basename($event['poster']) === $event['poster'] && is_file(UPLOAD_DIR . $event['poster'])) {
        @unlink(UPLOAD_DIR . $event['poster']);
    }
    flash('success', 'Event deleted successfully.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('danger', 'Could not delete event. Please try again.');
}
header('Location: events.php');
exit;
