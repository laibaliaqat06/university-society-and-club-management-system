<?php
class Clubs {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new club with extended metadata
    public function create($name, $description, $userId, $logo = null, $category = 'General', $mission = '', $vision = '', $coverImage = null) {
        $sql = "INSERT INTO clubs (name, description, logo, cover_image, category, mission, vision, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$name, $description, $logo, $coverImage, $category, $mission, $vision, $userId])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    // Update club details with metadata
    public function update($id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE clubs SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // Delete a club
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM clubs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Get all clubs
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM clubs ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    // Get a specific club by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clubs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Join a club with status handling
    public function joinClub($userId, $clubId, $status = 'approved') {
        if ($this->isMember($userId, $clubId)) {
            return false; 
        }
        $stmt = $this->pdo->prepare("INSERT INTO club_memberships (user_id, club_id, role, status) VALUES (?, ?, 'member', ?)");
        if (!$stmt->execute([$userId, $clubId, $status])) {
            die("<div style='background:#fff; color:#000; padding: 20px;'><h1>Database Insert Failed!</h1><p>" . print_r($stmt->errorInfo(), true) . "</p></div>");
        }
        return true;
    }

    // Update membership status (Approval System)
    public function updateMembershipStatus($userId, $clubId, $status) {
        $stmt = $this->pdo->prepare("UPDATE club_memberships SET status = ? WHERE user_id = ? AND club_id = ?");
        return $stmt->execute([$status, $userId, $clubId]);
    }

    // Check membership (including status)
    public function isMember($userId, $clubId, $includePending = true) {
        $sql = "SELECT status FROM club_memberships WHERE user_id = ? AND club_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $clubId]);
        $status = $stmt->fetchColumn();
        
        if (!$status) return false;
        if (!$includePending && $status !== 'approved') return false;
        return true;
    }

    // Get members of a club (filtered by status)
    public function getMembers($clubId, $status = 'approved') {
        $sql = "SELECT u.id, u.name, u.email, cm.role, cm.status, cm.joined_at 
                FROM club_memberships cm 
                JOIN users u ON cm.user_id = u.id 
                WHERE cm.club_id = ?";
        
        if ($status) {
            $sql .= " AND cm.status = ?";
            $stmt = $this->pdo->prepare($sql . " ORDER BY FIELD(cm.role, 'president', 'staff', 'coordinator', 'admin', 'member')");
            $stmt->execute([$clubId, $status]);
        } else {
            $stmt = $this->pdo->prepare($sql . " ORDER BY FIELD(cm.role, 'president', 'staff', 'coordinator', 'admin', 'member')");
            $stmt->execute([$clubId]);
        }
        return $stmt->fetchAll();
    }

    // Update member role
    public function updateMemberRole($userId, $clubId, $role) {
        $sql = "UPDATE club_memberships SET role = ? WHERE user_id = ? AND club_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$role, $userId, $clubId]);
    }

    // Remove member from club
    public function removeMember($userId, $clubId) {
        $stmt = $this->pdo->prepare("DELETE FROM club_memberships WHERE user_id = ? AND club_id = ?");
        return $stmt->execute([$userId, $clubId]);
    }
}
?>
