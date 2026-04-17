<?php
class Certificates {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function generate($userId, $eventId) {
        // Check if attendance is marked
        $stmt = $this->pdo->prepare("SELECT id FROM event_attendance WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$userId, $eventId]);
        if (!$stmt->fetch()) {
            return ["success" => false, "message" => "Attendance not marked for this student."];
        }

        // Check if certificate already exists
        $stmt = $this->pdo->prepare("SELECT certificate_hash FROM certificates WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$userId, $eventId]);
        $existing = $stmt->fetch();
        if ($existing) {
            return ["success" => true, "hash" => $existing['certificate_hash']];
        }

        // Generate a unique hash
        $hash = hash('sha256', $userId . $eventId . time() . uniqid());
        
        $stmt = $this->pdo->prepare("INSERT INTO certificates (user_id, event_id, certificate_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$userId, $eventId, $hash])) {
            return ["success" => true, "hash" => $hash];
        }
        
        return ["success" => false, "message" => "Failed to record certificate."];
    }

    public function getDetailsByHash($hash) {
        $sql = "SELECT c.*, u.name as student_name, e.title as event_name, e.event_date, cl.name as society_name, cl.logo as society_logo
                FROM certificates c
                JOIN users u ON c.user_id = u.id
                JOIN events e ON c.event_id = e.id
                LEFT JOIN clubs cl ON e.club_id = cl.id
                WHERE c.certificate_hash = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$hash]);
        return $stmt->fetch();
    }
}
?>
