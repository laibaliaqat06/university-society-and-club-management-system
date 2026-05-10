<?php
require_once '../../includes/session.php';
require_once '../../includes/db.php';
require_once '../../includes/Messages.php';
require_once '../../includes/Logger.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

$msgObj = new Messages($pdo);
$userId = $_SESSION['user_id'];
$contactId = $_POST['contact_id'];
$message = trim($_POST['message']);

if (!empty($message)) {
    $msgObj->sendMessage($userId, $contactId, $message);
    Logger::log("Sent Direct Message", "messages", $contactId, "Message sent to user ID $contactId");
    echo "success";
}
?>
