<?php
require 'config.php';
require 'functions.php';

[$where, $types, $params] = build_filters($_GET);   // exports what you searched/filtered

$stmt = $conn->prepare(
    "SELECT id, name, email, phone, course, status, created_at
     FROM students $where ORDER BY id"
);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="students_' . date('Ymd_His') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Course', 'Status', 'Created At']);
while ($row = $result->fetch_assoc()) {
    fputcsv($out, $row);
}
fclose($out);
exit;