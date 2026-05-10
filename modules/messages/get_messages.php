<?php
require_once '../../includes/session.php';
require_once '../../includes/db.php';
require_once '../../includes/Messages.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['contact_id'])) {
    exit;
}

$msgObj = new Messages($pdo);
$userId = $_SESSION['user_id'];
$contactId = $_GET['contact_id'];

$history = $msgObj->getChatHistory($userId, $contactId);

foreach ($history as $msg) {
    $isMe = ($msg['sender_id'] == $userId);
    $time = date('H:i', strtotime($msg['created_at']));
    ?>
    <div class="d-flex <?= $isMe ? 'justify-content-end' : 'justify-content-start' ?> mb-3">
        <div class="message-bubble <?= $isMe ? 'bg-primary text-white' : 'bg-light text-dark' ?> p-3 rounded-4 shadow-sm" style="max-width: 75%;">
            <p class="mb-1"><?= htmlspecialchars($msg['message']) ?></p>
            <small class="<?= $isMe ? 'text-white-50' : 'text-muted' ?> extra-small"><?= $time ?></small>
        </div>
    </div>
    <?php
}
?>
