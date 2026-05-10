<?php
require_once '../../../includes/header.php';
require_once '../../../includes/Events.php';

$eventsObj = new Events($pdo);

if ($_SESSION['role'] !== 'super_admin') {
    header("Location: " . BASE_URL . "dashboards/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $eventId = $_POST['event_id'];
    $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
    $reason = $_POST['rejection_reason'] ?? '';
    
    $eventsObj->updateAdminStatus($eventId, $status, $_SESSION['user_id'], $reason);
    $success = "Event status updated successfully!";
}

$pendingEvents = $eventsObj->getPendingAdmin();
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <h1 class="display-4 fw-bold text-white">Event Proposal Review</h1>
        <p class="lead text-white-50">Approve or reject event proposals before they go to Finance.</p>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (empty($pendingEvents)): ?>
            <div class="glass-card p-5 text-center text-white-50">
                <i class="bi bi-check2-circle display-1 mb-3"></i>
                <h3>All caught up!</h3>
                <p>No pending event proposals to review.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($pendingEvents as $event): ?>
                    <div class="col-12 mb-4">
                        <div class="glass-card p-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <span class="badge bg-primary mb-2"><?= htmlspecialchars($event['club_name']) ?></span>
                                    <h3 class="text-white fw-bold"><?= htmlspecialchars($event['title']) ?></h3>
                                    <p class="text-white-50"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                                    <div class="d-flex gap-4 text-white-50 small mb-4">
                                        <span><i class="bi bi-calendar-event me-2 text-primary"></i> <?= date('F j, Y, g:i a', strtotime($event['event_date'])) ?></span>
                                        <span><i class="bi bi-geo-alt me-2 text-danger"></i> <?= htmlspecialchars($event['location']) ?></span>
                                        <span><i class="bi bi-cash me-2 text-success"></i> Budget: $<?= number_format($event['budget_amount'], 2) ?></span>
                                    </div>
                                    <div class="p-3 bg-dark bg-opacity-50 rounded border border-white border-opacity-10 mb-3">
                                        <h6 class="fw-bold text-white small">Budget Details:</h6>
                                        <p class="small text-white-50 mb-0"><?= nl2br(htmlspecialchars($event['budget_details'] ?? 'No details provided.')) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex flex-column justify-content-center">
                                    <form method="POST" class="d-grid gap-2">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-success btn-lg rounded-pill">
                                            <i class="bi bi-check-lg"></i> Approve Phase 1
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-lg rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $event['id'] ?>">
                                            <i class="bi bi-x-lg"></i> Reject Proposal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal<?= $event['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" class="modal-content glass-card border-0">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <div class="modal-header border-bottom border-white border-opacity-10">
                                        <h5 class="modal-title text-white">Reject Proposal</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea name="rejection_reason" class="form-control bg-dark text-white border-secondary" rows="4" placeholder="Enter reason for rejection..." required></textarea>
                                    </div>
                                    <div class="modal-footer border-top border-white border-opacity-10">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger">Confirm Rejection</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../../includes/footer.php'; ?>
