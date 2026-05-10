<?php
require_once '../../includes/header.php';
require_once '../../includes/Messages.php';

$msgObj = new Messages($pdo);
$userId = $_SESSION['user_id'];
$conversations = $msgObj->getConversations($userId);
$activeContactId = $_GET['id'] ?? ($conversations[0]['contact_id'] ?? null);

// If ID is passed but not in current conversations (new chat)
if (isset($_GET['id']) && !array_filter($conversations, fn($c) => $c['contact_id'] == $_GET['id'])) {
    $stmt = $pdo->prepare("SELECT id as contact_id, name as contact_name, role as contact_role FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $newContact = $stmt->fetch();
    if ($newContact) {
        $newContact['last_message'] = "Start a new conversation...";
        $newContact['last_time'] = null;
        $newContact['unread_count'] = 0;
        array_unshift($conversations, $newContact);
    }
}
?>

<div class="row g-0 h-100 chat-layout overflow-hidden" style="margin: -1rem; height: calc(100vh - 150px) !important;">
    <!-- Conversations Sidebar -->
    <div class="col-md-4 col-xl-3 border-end bg-body h-100 overflow-auto">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">Messages</h5>
            <button class="btn btn-sm btn-outline-primary rounded-circle"><i class="bi bi-pencil-square"></i></button>
        </div>
        <div class="list-group list-group-flush conversation-list">
            <?php foreach ($conversations as $c): ?>
                <a href="?id=<?= $c['contact_id'] ?>" class="list-group-item list-group-item-action p-3 border-bottom-0 <?= ($activeContactId == $c['contact_id']) ? 'active bg-primary bg-opacity-10 border-start border-4 border-primary' : '' ?>">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-secondary bg-opacity-25 rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center fw-bold text-primary">
                            <?= strtoupper(substr($c['contact_name'], 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0 fw-bold text-truncate"><?= htmlspecialchars($c['contact_name']) ?></h6>
                                <small class="text-muted extra-small"><?= $c['last_time'] ? date('H:i', strtotime($c['last_time'])) : '' ?></small>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0 x-small text-muted text-truncate"><?= htmlspecialchars($c['last_message'] ?? '') ?></p>
                                <?php if ($c['unread_count'] > 0): ?>
                                    <span class="badge rounded-pill bg-danger"><?= $c['unread_count'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (empty($conversations)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots display-4 mb-3 d-block"></i>
                    <p>No messages yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chat Window -->
    <div class="col-md-8 col-xl-9 d-flex flex-column h-100 bg-white">
        <?php if ($activeContactId): 
            $activeContact = array_values(array_filter($conversations, fn($c) => $c['contact_id'] == $activeContactId))[0] ?? null;
        ?>
            <div class="p-3 border-bottom d-flex align-items-center shadow-sm">
                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px;">
                    <?= strtoupper(substr($activeContact['contact_name'], 0, 1)) ?>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($activeContact['contact_name']) ?></h6>
                    <small class="text-success extra-small"><i class="bi bi-circle-fill me-1"></i> Active Now</small>
                </div>
            </div>

            <div id="chat-messages" class="flex-grow-1 p-4 overflow-auto bg-light bg-opacity-50" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">
                <!-- Messages will be loaded here via AJAX -->
            </div>

            <div class="p-3 border-top bg-white">
                <form id="chat-form" class="d-flex gap-2">
                    <input type="hidden" name="contact_id" value="<?= $activeContactId ?>">
                    <input type="text" id="message-input" name="message" class="form-control rounded-pill px-4" placeholder="Type your message..." autocomplete="off">
                    <button type="submit" class="btn btn-primary rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center text-center">
                <div>
                    <img src="<?= BASE_URL ?>assets/img/messaging-empty.svg" style="width: 200px; opacity: 0.5;">
                    <h5 class="mt-4 text-muted">Select a conversation to start chatting</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .chat-layout { background: #fff; }
    .extra-small { font-size: 0.65rem; }
    .x-small { font-size: 0.75rem; }
    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #e0e0e0; border-radius: 10px; }
    .message-bubble { position: relative; }
    .conversation-list .active { background-color: rgba(99, 102, 241, 0.08) !important; color: inherit !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const contactId = '<?= $activeContactId ?>';

    if (!contactId) return;

    function loadMessages() {
        fetch('get_messages.php?contact_id=' + contactId)
            .then(response => response.text())
            .then(html => {
                const wasAtBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop <= messagesContainer.clientHeight + 100;
                messagesContainer.innerHTML = html;
                if (wasAtBottom) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            });
    }

    // Load initial messages
    loadMessages();
    // Poll for new messages every 3 seconds
    setInterval(loadMessages, 3000);

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        const formData = new FormData(chatForm);
        fetch('send_message.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text())
        .then(res => {
            if (res === 'success') {
                messageInput.value = '';
                loadMessages();
            }
        });
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
