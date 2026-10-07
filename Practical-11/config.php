<?php
// Throw exceptions on MySQLi errors so we can show clear failure messages
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli('localhost', 'root', '', 'student_db');
$conn->set_charset('utf8mb4');