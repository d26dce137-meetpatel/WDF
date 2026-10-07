<?php

session_start();

$host = "localhost";
$dbname = "practical10_auth";
$dbuser = "root";
$dbpass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


/* Session timeout: 5 minutes */
$timeout = 300;

if (isset($_SESSION['user_id'])) {

    if (isset($_SESSION['last_activity'])) {

        if (time() - $_SESSION['last_activity'] > $timeout) {

            session_unset();
            session_destroy();

            header("Location: login.php?timeout=1");
            exit;
        }
    }

    $_SESSION['last_activity'] = time();
}


/* Remember Me */
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {

    $token = $_COOKIE['remember_token'];
    $token_hash = hash('sha256', $token);

    $stmt = $pdo->prepare("
        SELECT users.*
        FROM users
        INNER JOIN remember_tokens
        ON users.id = remember_tokens.user_id
        WHERE remember_tokens.token_hash = ?
        AND remember_tokens.expires_at > NOW()
    ");

    $stmt->execute([$token_hash]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();
    }
}


/* Check login */
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}


/* Check role */
function requireRole($role)
{
    requireLogin();

    if ($_SESSION['role'] !== $role) {

        http_response_code(403);

        echo "<h1>403 - Unauthorized Access</h1>";
        echo "<p>You do not have permission to access this page.</p>";
        echo "<a href='dashboard.php'>Go to Dashboard</a>";

        exit;
    }
}

?>