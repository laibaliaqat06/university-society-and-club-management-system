<?php
class Announcements {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createGlobal($title, $message, $createdBy, $type = 'info', $isPinned = 0, $validUntil = null) {
        $stmt = $this->pdo->prepare("INSERT INTO announcements (title, message, type, is_pinned, valid_until, society_id, created_by) VALUES (?, ?, ?, ?, ?, NULL, ?)");
        return $stmt->execute([$title, $message, $type, $isPinned, $validUntil, $createdBy]);
    }

    public function createForSociety($title, $message, $societyId, $createdBy, $type = 'info', $isPinned = 0, $validUntil = null) {
        $stmt = $this->pdo->prepare("INSERT INTO announcements (title, message, type, is_pinned, valid_until, society_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $message, $type, $isPinned, $validUntil, $societyId, $createdBy]);
    }

    public function getLatestGlobal($limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.name as author_name 
            FROM announcements a 
            LEFT JOIN users u ON a.created_by = u.id 
            WHERE a.society_id IS NULL 
            AND (a.valid_until > NOW() OR a.valid_until IS NULL)
            ORDER BY a.is_pinned DESC, a.created_at DESC 
            LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLatestForSociety($societyId, $limit = 5) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.name as author_name 
            FROM announcements a 
            LEFT JOIN users u ON a.created_by = u.id 
            WHERE a.society_id = ? 
            AND (a.valid_until > NOW() OR a.valid_until IS NULL)
            ORDER BY a.is_pinned DESC, a.created_at DESC 
            LIMIT ?");
        $stmt->bindValue(1, $societyId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getVisibleAnnouncements($userId, $role) {
        // Super admins see global
        if ($role === 'super_admin') {
            return $this->getLatestGlobal(20);
        }
        
        // Members see global + clubs they joined
        $sql = "SELECT a.*, u.name as author_name, c.name as society_name 
                FROM announcements a 
                LEFT JOIN users u ON a.created_by = u.id
                LEFT JOIN clubs c ON a.society_id = c.id
                WHERE (a.society_id IS NULL OR a.society_id IN (SELECT club_id FROM club_memberships WHERE user_id = ?))
                AND (a.valid_until > NOW() OR a.valid_until IS NULL)
                ORDER BY a.is_pinned DESC, a.created_at DESC 
                LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
?>
