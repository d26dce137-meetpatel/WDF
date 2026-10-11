<?php
require_once 'config.php';
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
header('Location: events.php');
exit;
