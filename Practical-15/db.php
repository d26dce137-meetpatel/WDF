<?php
// StudentHub database connection (XAMPP defaults)
$DB_HOST = 'localhost'; $DB_NAME = 'studenthub_practical7_15'; $DB_USER = 'root'; $DB_PASS = '';
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch(PDOException $e) { http_response_code(500); exit('Database connection failed. Import database.sql in phpMyAdmin and check db.php settings.'); }
if (session_status() === PHP_SESSION_NONE) session_start();

// Contact inbox: student messages are stored in MySQL so admins can view them after login.
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(190) NOT NULL,
        subject VARCHAR(180) NOT NULL DEFAULT 'Student Portal Contact',
        message TEXT NOT NULL,
        status ENUM('unread','read') NOT NULL DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_contact_status (status),
        INDEX idx_contact_created (created_at),
        CONSTRAINT fk_contact_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Throwable $e) {
    // If schema creation is restricted, create the table by importing database.sql manually.
}

// Small compatibility migrations so existing XAMPP databases can use Trash/Restore.
try {
    $cols = $pdo->query("SHOW COLUMNS FROM events LIKE 'deleted_at'")->fetch();
    if (!$cols) $pdo->exec("ALTER TABLE events ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL");
    $statusCol = $pdo->query("SHOW COLUMNS FROM students LIKE 'status'")->fetch();
    if ($statusCol && strpos(strtolower($statusCol['Type']), "'deleted'") === false) {
        $pdo->exec("ALTER TABLE students MODIFY status ENUM('active','inactive','deleted') NOT NULL DEFAULT 'active'");
    }
} catch (Throwable $e) {
    // If the database account cannot alter schema, import the updated database.sql manually.
}
?>
