<?php
require_once '../../includes/session.php';
require_once '../../includes/Events.php';
$eventsObj = new Events($pdo);

$eventId = $_GET['id'] ?? 0;
if (!$eventId) {
    header("Location: manage.php");
    exit;
}

// Security Check: Only Society Head or Event Manager who created it
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

$event = $eventsObj->getById($eventId);

if (!$event) {
    require_once '../../includes/header.php';
    die('<div class="alert alert-danger m-5">Event not found.</div>');
}

// Only allow creator or society head of that club
$cStmt = $pdo->prepare("SELECT created_by FROM clubs WHERE id = ?");
$cStmt->execute([$event['club_id']]);
$clubCreator = $cStmt->fetchColumn();

if ($role !== 'super_admin' && $event['created_by'] != $userId && $clubCreator != $userId) {
    require_once '../../includes/header.php';
    die('<div class="alert alert-danger m-5">Access Denied: You cannot manage attendance for this event.</div>');
}

// Handle Status Updates
if (isset($_POST['update_status'])) {
    $eId = $_POST['enrollment_id'];
    $status = $_POST['status'];
    $eventsObj->updateEnrollmentStatus($eId, $status);
}

// Handle Attendance Marking
if (isset($_POST['mark_user'])) {
    $uId = $_POST['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO event_attendance (event_id, user_id, marked_by) VALUES (?, ?, ?)");
        $stmt->execute([$eventId, $uId, $userId]);
    } catch (Exception $e) {}
}

if (isset($_POST['remove_user'])) {
    $uId = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM event_attendance WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$eventId, $uId]);
}

// Fetch Enrollments and current attendance
$enrollments = $eventsObj->getEnrollments($eventId);

$attendanceStmt = $pdo->prepare("SELECT user_id FROM event_attendance WHERE event_id = ?");
$attendanceStmt->execute([$eventId]);
$attendedIds = $attendanceStmt->fetchAll(PDO::FETCH_COLUMN);

require_once '../../includes/header.php';
?>

<div class="app-content-header">
    <div class="container-fluid">
        <h3 class="fw-bold"><i class="bi bi-person-check me-2"></i> Attendance: <?= htmlspecialchars($event['title']) ?></h3>
        <p class="text-muted"><?= date('M d, Y', strtotime($event['event_date'])) ?> • <?= htmlspecialchars($event['location']) ?></p>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card glass-card border-0 shadow-lg">
            <div class="card-header border-bottom border-white border-opacity-10 py-3">
                <h5 class="mb-0 fw-bold">Student List (Enrollments)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Student Info</th>
                                <th>Contact</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Attendance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($enrollments as $e): ?>
                                <?php $isAttended = in_array($e['user_id'], $attendedIds); ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($e['student_name']) ?></strong><br>
                                        <small class="text-muted">Reg #: <?= htmlspecialchars($e['registration_no']) ?></small>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($e['student_email']) ?></small><br>
                                        <small><?= htmlspecialchars($e['student_phone']) ?></small>
                                    </td>
                                    <td><small class="text-wrap" style="max-width: 200px; display: block;"><?= htmlspecialchars($e['message']) ?></small></td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="enrollment_id" value="<?= $e['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit();">
                                                <option value="pending" <?= $e['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="approved" <?= $e['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                                                <option value="rejected" <?= $e['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td>
                                        <?php if($isAttended): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Present</span>
                                        <?php elseif($e['status'] == 'approved'): ?>
                                            <span class="badge bg-secondary">Absent</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">Not Approved</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($e['status'] == 'approved'): ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="user_id" value="<?= $e['user_id'] ?>">
                                                <?php if(!$isAttended): ?>
                                                    <button type="submit" name="mark_user" class="btn btn-sm btn-primary">Mark Present</button>
                                                <?php else: ?>
                                                    <button type="submit" name="remove_user" class="btn btn-sm btn-outline-danger">Unmark</button>
                                                    <a href="<?= BASE_URL ?>modules/certificates/generate.php?event_id=<?= $eventId ?>&user_id=<?= $e['user_id'] ?>" class="btn btn-sm btn-success ms-2">
                                                        <i class="bi bi-patch-check"></i> Certificate
                                                    </a>
                                                <?php endif; ?>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light disabled" title="Approve application first">Mark Present</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(empty($enrollments)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No enrollments found for this event.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top border-white border-opacity-10 py-3">
                <a href="manage.php" class="btn btn-secondary px-4">Back to Events</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

