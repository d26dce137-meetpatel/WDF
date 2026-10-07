<?php
require 'config.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');          // never delete through a plain link
}
check_csrf();

$id     = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

try {
    if ($action === 'soft') {
        $stmt = $conn->prepare("UPDATE students SET status = 'deleted' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        flash($ok ? 'success' : 'error',
              $ok ? 'Student moved to deleted list.' : 'Student not found.');

    } elseif ($action === 'restore') {
        $stmt = $conn->prepare("UPDATE students SET status = 'active' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        flash($ok ? 'success' : 'error',
              $ok ? 'Student restored successfully.' : 'Student not found.');

    } elseif ($action === 'permanent') {
        // Real DELETE, allowed only for already soft-deleted records
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ? AND status = 'deleted'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        flash($ok ? 'success' : 'error',
              $ok ? 'Student permanently deleted.' : 'Record not found or not in deleted list.');

    } else {
        flash('error', 'Invalid action.');
    }
} catch (mysqli_sql_exception $ex) {
    flash('error', 'Database error: operation failed.');
}

redirect('index.php');