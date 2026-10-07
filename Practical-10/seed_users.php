<?php

require_once "auth.php";

$adminPassword = password_hash(
    "Admin@123",
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare("
    INSERT INTO users
    (username, password, role)
    VALUES (?, ?, ?)
");

$stmt->execute([
    "admin",
    $adminPassword,
    "admin"
]);

$studentPassword = password_hash(
    "Student@123",
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare("
    INSERT INTO users
    (username, password, role)
    VALUES (?, ?, ?)
");

$stmt->execute([
    "student",
    $studentPassword,
    "student"
]);


echo "<h2>Users created successfully!</h2>";

echo "<p>Admin Username: admin</p>";
echo "<p>Admin Password: Admin@123</p>";

echo "<p>Student Username: student</p>";
echo "<p>Student Password: Student@123</p>";

echo "<p><b>Delete seed_users.php after running it.</b></p>";

?>