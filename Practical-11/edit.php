<?php
require 'config.php';
require 'functions.php';

$id = (int)($_GET['id'] ?? 0);

// Load the existing record
$stmt = $conn->prepare("SELECT id, name, email, phone, course FROM students WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    flash('error', 'Student not found.');
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $data = [
        'name'   => trim($_POST['name'] ?? ''),
        'email'  => trim($_POST['email'] ?? ''),
        'phone'  => trim($_POST['phone'] ?? ''),
        'course' => trim($_POST['course'] ?? ''),
    ];
    $errors = validate_student($data);

    if (!$errors) {
        try {
            $stmt = $conn->prepare(
                "UPDATE students SET name = ?, email = ?, phone = ?, course = ? WHERE id = ?"
            );
            $stmt->bind_param('ssssi', $data['name'], $data['email'], $data['phone'], $data['course'], $id);
            $stmt->execute();

            flash('success', 'Student updated successfully.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            $errors[] = ($ex->getCode() === 1062)
                ? 'This email belongs to another student.'
                : 'Database error: could not update the student.';
        }
    }
}

$action = 'edit.php?id=' . $id;
$button = 'Update Student';
require 'header.php';
echo '<h2>Edit Student</h2>';
require 'form.php';
require 'footer.php';