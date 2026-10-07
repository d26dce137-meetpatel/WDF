<?php

require_once "auth.php";


/* Delete remember-me token */
if (isset($_COOKIE['remember_token'])) {

    $token = $_COOKIE['remember_token'];

    $token_hash = hash('sha256', $token);

    $stmt = $pdo->prepare(
        "DELETE FROM remember_tokens WHERE token_hash = ?"
    );

    $stmt->execute([$token_hash]);


    /* Delete cookie */
    setcookie(
        "remember_token",
        "",
        time() - 3600,
        "/"
    );
}


/* Destroy session */
$_SESSION = [];

session_destroy();


header("Location: login.php");

exit;

?>