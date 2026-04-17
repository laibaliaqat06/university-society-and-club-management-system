<?php
require_once '../core/session.php';
require_once '../core/Notifications.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$notifObj = new Notifications($pdo);

if (isset($_GET['id'])) {
    $notifObj->markAsRead($_SESSION['user_id'], $_GET['id']);
} else {
    $notifObj->markAsRead($_SESSION['user_id']);
}

$referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . "index.php";
header("Location: " . $referer);
exit;
?>
