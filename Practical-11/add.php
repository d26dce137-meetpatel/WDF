<?php
require 'config.php';
require 'functions.php';

$errors = [];
$data = ['name' => '', 'email' => '', 'phone' => '', 'course' => ''];

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
                "INSERT INTO students (name, email, phone, course) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param('ssss', $data['name'], $data['email'], $data['phone'], $data['course']);
            $stmt->execute();

            flash('success', 'Student added successfully.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            $errors[] = ($ex->getCode() === 1062)
                ? 'This email is already registered.'
                : 'Database error: could not add the student.';
        }
    }
}

$action = 'add.php';
$button = 'Add Student';
require 'header.php';
echo '<h2>Add Student</h2>';
require 'form.php';
require 'footer.php';