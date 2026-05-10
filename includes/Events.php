<?php
class Events {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Create a new event proposal
    public function create($clubId, $title, $description, $date, $location, $userId, $budget = 0, $details = '') {
        // Check for duplicates
        $check = $this->pdo->prepare("SELECT id FROM events WHERE club_id = ? AND title = ? AND event_date = ?");
        $check->execute([$clubId, $title, $date]);
        if ($check->fetch()) {
            return false;
        }

        $sql = "INSERT INTO events (club_id, title, description, event_date, location, created_by, budget_amount, budget_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$clubId, $title, $description, $date, $location, $userId, $budget, $details]);
    }

    // Get approved events for a specific club
    public function getByClub($clubId) {
        $stmt = $this->pdo->prepare("SELECT * FROM events WHERE club_id = ? AND admin_status = 'approved' AND finance_status = 'approved' ORDER BY event_date ASC");
        $stmt->execute([$clubId]);
        return $stmt->fetchAll();
    }

    // Get all upcoming FULLY APPROVED events
    public function getAllUpcoming() {
        $stmt = $this->pdo->query("SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.event_date >= CURDATE() AND e.admin_status = 'approved' AND e.finance_status = 'approved' ORDER BY e.event_date ASC");
        return $stmt->fetchAll();
    }

    // Get all past events
    public function getPastEvents() {
        $stmt = $this->pdo->query("SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.event_date < CURDATE() AND e.admin_status = 'approved' AND e.finance_status = 'approved' ORDER BY e.event_date DESC");
        return $stmt->fetchAll();
    }

    // Find event by ID with club and creator info
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT e.*, c.name as club_name, u.name as creator_name 
            FROM events e 
            JOIN clubs c ON e.club_id = c.id 
            LEFT JOIN users u ON e.created_by = u.id 
            WHERE e.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // RSVP to an event
    public function rsvp($eventId, $userId, $status) {
        // Check if already RSVP'd
        $check = $this->pdo->prepare("SELECT id FROM event_rsvps WHERE event_id = ? AND user_id = ?");
        $check->execute([$eventId, $userId]);
        
        if ($check->rowCount() > 0) {
            // Update
            $sql = "UPDATE event_rsvps SET status = ? WHERE event_id = ? AND user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$status, $eventId, $userId]);
        } else {
            // Insert
            $sql = "INSERT INTO event_rsvps (event_id, user_id, status) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$eventId, $userId, $status]);
        }
    }

    // Get RSVPs for an event
    public function getRsvps($eventId) {
        $sql = "SELECT u.name, er.status FROM event_rsvps er JOIN users u ON er.user_id = u.id WHERE er.event_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    // Submit a formal enrollment application
    public function enroll($eventId, $userId, $data) {
        $sql = "INSERT INTO event_enrollments (event_id, user_id, student_name, student_email, student_phone, message) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $eventId, 
            $userId, 
            $data['name'], 
            $data['email'], 
            $data['phone'], 
            $data['message']
        ]);
    }

    // Get all enrollments for an event (Admin view)
    public function getEnrollments($eventId) {
        $sql = "SELECT ee.*, u.registration_no 
                FROM event_enrollments ee 
                JOIN users u ON ee.user_id = u.id 
                WHERE ee.event_id = ? 
                ORDER BY ee.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    // Update enrollment status
    public function updateEnrollmentStatus($enrollmentId, $status) {
        $sql = "UPDATE event_enrollments SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $enrollmentId]);
    }

    // Check if user has already applied
    public function getUserEnrollment($eventId, $userId) {
        $sql = "SELECT status FROM event_enrollments WHERE event_id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$eventId, $userId]);
        return $stmt->fetch();
    }

    // --- Approval Workflow Methods ---

    public function getPendingAdmin() {
        $sql = "SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.admin_status = 'pending' ORDER BY e.created_at DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getPendingFinance() {
        $sql = "SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.admin_status = 'approved' AND e.finance_status = 'pending' ORDER BY e.created_at DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function updateAdminStatus($eventId, $status, $userId, $reason = '') {
        $sql = "UPDATE events SET admin_status = ?, admin_approved_by = ?, rejection_reason = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $userId, $reason, $eventId]);
    }

    public function updateFinanceStatus($eventId, $status, $userId, $reason = '') {
        $sql = "UPDATE events SET finance_status = ?, finance_approved_by = ?, rejection_reason = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $userId, $reason, $eventId]);
    }

    public function getMyProposedEvents($userId) {
        $sql = "SELECT e.*, c.name as club_name FROM events e JOIN clubs c ON e.club_id = c.id WHERE e.created_by = ? ORDER BY e.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // --- Gallery Methods ---

    public function getGallery($eventId) {
        $stmt = $this->pdo->prepare("SELECT * FROM event_gallery WHERE event_id = ? ORDER BY upload_date DESC");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function addGalleryImage($eventId, $image) {
        $stmt = $this->pdo->prepare("INSERT INTO event_gallery (event_id, image) VALUES (?, ?)");
        return $stmt->execute([$eventId, $image]);
    }

    public function deleteGalleryImage($imageId) {
        // First get the image path to delete the file
        $stmt = $this->pdo->prepare("SELECT image FROM event_gallery WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetchColumn();

        if ($image) {
            $filePath = __DIR__ . '/../' . $image;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $stmt = $this->pdo->prepare("DELETE FROM event_gallery WHERE id = ?");
            return $stmt->execute([$imageId]);
        }
        return false;
    }
}
?>
