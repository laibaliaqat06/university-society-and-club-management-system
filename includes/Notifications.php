<?php
class Notifications {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function dispatchToAll($announcementId) {
        // Send to everyone 
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, announcement_id) SELECT id, ? FROM users WHERE is_active = 1");
        return $stmt->execute([$announcementId]);
    }

    public function dispatchToSociety($announcementId, $societyId) {
        // Send only to society members
        $stmt = $this->pdo->prepare("INSERT INTO notifications (user_id, announcement_id) SELECT user_id, ? FROM club_memberships WHERE club_id = ? AND status = 'approved'");
        return $stmt->execute([$announcementId, $societyId]);
    }

    public function getUnread($userId, $limit = 5) {
        $sql = "SELECT n.id as notif_id, a.title, a.created_at, c.name as society_name 
                FROM notifications n 
                JOIN announcements a ON n.announcement_id = a.id
                LEFT JOIN clubs c ON a.society_id = c.id
                WHERE n.user_id = ? AND n.is_read = 0 
                ORDER BY n.created_at DESC LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function countUnread($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($userId, $notifId = null) {
        if ($notifId) {
            $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND id = ?");
            return $stmt->execute([$userId, $notifId]);
        } else {
            // Mark all read
            $stmt = $this->pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            return $stmt->execute([$userId]);
        }
    }
}
?>
