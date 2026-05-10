<?php
class Messages {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function sendMessage($senderId, $receiverId, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        return $stmt->execute([$senderId, $receiverId, $message]);
    }

    public function getConversations($userId) {
        $query = "SELECT DISTINCT 
                    CASE WHEN sender_id = :userId THEN receiver_id ELSE sender_id END as contact_id,
                    u.name as contact_name,
                    u.role as contact_role,
                    (SELECT message FROM messages 
                     WHERE (sender_id = :userId AND receiver_id = contact_id) 
                        OR (sender_id = contact_id AND receiver_id = :userId) 
                     ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM messages 
                     WHERE (sender_id = :userId AND receiver_id = contact_id) 
                        OR (sender_id = contact_id AND receiver_id = :userId) 
                     ORDER BY created_at DESC LIMIT 1) as last_time,
                    (SELECT COUNT(*) FROM messages 
                     WHERE sender_id = contact_id AND receiver_id = :userId AND is_read = 0) as unread_count
                  FROM messages m
                  JOIN users u ON u.id = (CASE WHEN sender_id = :userId THEN receiver_id ELSE sender_id END)
                  WHERE sender_id = :userId OR receiver_id = :userId
                  ORDER BY last_time DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public function getChatHistory($userId, $contactId) {
        // Mark as read
        $this->markAsRead($userId, $contactId);

        $query = "SELECT * FROM messages 
                  WHERE (sender_id = :userId AND receiver_id = :contactId) 
                     OR (sender_id = :contactId AND receiver_id = :userId) 
                  ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['userId' => $userId, 'contactId' => $contactId]);
        return $stmt->fetchAll();
    }

    public function markAsRead($userId, $contactId) {
        $stmt = $this->pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
        $stmt->execute([$contactId, $userId]);
    }

    public function countUnreadGlobal($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
}
?>
