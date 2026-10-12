<?php
// Run this once locally, then delete this file for safety.
require_once __DIR__ . '/includes/db.php';
$name = 'Administrator';
$email = 'admin@example.com';
$plainPassword = 'Admin@12345';
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name,email,password,role,status) VALUES (?,?,?,?, 'active')");
try {
    $stmt->execute([$name, $email, $hash, 'admin']);
    echo "Admin created. Email: admin@example.com Password: Admin@12345. Delete create_admin.php now.";
} catch (PDOException $e) {
    echo "Could not create admin (email may already exist). Delete create_admin.php after setup.";
}
