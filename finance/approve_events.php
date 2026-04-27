<?php
require_once '../includes/header.php';
require_once '../core/Events.php';

$eventsObj = new Events($pdo);

if ($_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'finance_manager') {
    header("Location: " . BASE_URL . "dashboards/finance_manager.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $eventId = $_POST['event_id'];
    $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
    $reason = $_POST['rejection_reason'] ?? '';
    
    $eventsObj->updateFinanceStatus($eventId, $status, $_SESSION['user_id'], $reason);
    $success = "Budget status updated successfully!";
}

$pendingEvents = $eventsObj->getPendingFinance();
$debug_info = [
    'role' => $_SESSION['role'],
    'user_id' => $_SESSION['user_id'],
    'pending_count' => count($pendingEvents)
];
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: #1e1b4b url('https://images.unsplash.com/photo-1454165833767-027ee21951ee?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2); z-index: 0;"></div>
    <div class="container position-relative py-5" style="z-index: 1;">
        <h1 class="display-4 fw-bold text-white">Finance Budget Approval</h1>
        <p class="lead text-white-50">Review budget requests for events already approved by the administration.</p>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (empty($pendingEvents)): ?>
            <div class="glass-card p-5 text-center">
                <i class="bi bi-wallet2 display-1 mb-3 text-muted"></i>
                <h3 class="fw-bold">Finance queue clear!</h3>
                <p class="text-muted">No pending budget applications to review. (Debug: Role=<?= $_SESSION['role'] ?>)</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($pendingEvents as $event): ?>
                    <div class="col-12 mb-4">
                        <div class="glass-card p-4 border border-success border-opacity-10">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-success me-3">Admin Approved</span>
                                        <span class="text-muted small">Society: <?= htmlspecialchars($event['club_name']) ?></span>
                                    </div>
                                    <h3 class="fw-bold mb-3"><?= htmlspecialchars($event['title']) ?></h3>
                                    
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-4">
                                            <div class="p-3 rounded bg-success bg-opacity-10 border border-success border-opacity-25">
                                                <label class="d-block x-small text-success fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">Requested Budget</label>
                                                <h4 class="mb-0 fw-bold">$<?= number_format($event['budget_amount'], 2) ?></h4>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="p-3 rounded bg-body-tertiary border border-white border-opacity-10 h-100">
                                                <label class="d-block x-small text-muted fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">Budget Breakdown</label>
                                                <p class="small mb-0"><?= nl2br(htmlspecialchars($event['budget_details'] ?? 'No breakdown provided.')) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-4 text-muted small">
                                        <span><i class="bi bi-calendar-event me-2"></i> Event Date: <?= date('M d, Y', strtotime($event['event_date'])) ?></span>
                                        <span><i class="bi bi-geo-alt me-2"></i> <?= htmlspecialchars($event['location']) ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex flex-column justify-content-center gap-2">
                                    <form method="POST" class="d-grid gap-2">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-premium btn-lg rounded-pill shadow">
                                            <i class="bi bi-check2-all me-1"></i> Approve Budget
                                        </button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $event['id'] ?>">
                                            <i class="bi bi-x-circle me-1"></i> Deny Budget
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
                                        <h5 class="modal-title">Deny Budget Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Enter reason for budget denial (e.g. Too high, insufficient funds)..." required></textarea>
                                    </div>
                                    <div class="modal-footer border-top border-white border-opacity-10">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger">Confirm Denial</button>
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

<?php require_once '../includes/footer.php'; ?>
