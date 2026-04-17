<?php
require_once '../includes/header.php';
require_once '../core/Events.php';

$eventsObj = new Events($pdo);
$userId = $_SESSION['user_id'];

// Fetch all enrollments for this user
$sql = "SELECT ee.*, e.title, e.event_date, e.location, c.name as club_name, c.logo 
        FROM event_enrollments ee 
        JOIN events e ON ee.event_id = e.id 
        JOIN clubs c ON e.club_id = c.id 
        WHERE ee.user_id = ? 
        ORDER BY ee.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId]);
$myEnrollments = $stmt->fetchAll();
?>

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="fw-bold text-white">My event Enrollments</h3>
                <p class="text-white-50">Track your applications and participation history</p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row g-4">
            <?php if (empty($myEnrollments)): ?>
                <div class="col-12 text-center py-5">
                    <div class="glass-card p-5 d-inline-block">
                        <i class="bi bi-calendar-x text-white-50 display-1 mb-4"></i>
                        <h4 class="text-white">No enrollments yet</h4>
                        <p class="text-white-50">You haven't applied for any events. Browse upcoming events to get started!</p>
                        <a href="<?= BASE_URL ?>events/index.php" class="btn btn-premium px-5 mt-3">Explore Events</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($myEnrollments as $en): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card glass-card h-100 border-0 shadow-lg overflow-hidden hvr-float">
                            <div class="card-header border-bottom border-white border-opacity-10 py-3 bg-white bg-opacity-5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-opacity-10 
                                        <?= $en['status'] == 'approved' ? 'bg-success text-success' : ($en['status'] == 'rejected' ? 'bg-danger text-danger' : 'bg-warning text-warning') ?> 
                                        px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-<?= $en['status'] == 'approved' ? 'check-circle-fill' : ($en['status'] == 'rejected' ? 'x-circle-fill' : 'clock-history') ?> me-1"></i>
                                        <?= strtoupper($en['status']) ?>
                                    </span>
                                    <small class="text-white-50"><?= date('M d, Y', strtotime($en['created_at'])) ?></small>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= !empty($en['logo']) ? BASE_URL . $en['logo'] : BASE_URL . 'assets/img/default-logo.png' ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                                    <div>
                                        <h6 class="mb-0 text-white-50 small"><?= htmlspecialchars($en['club_name']) ?></h6>
                                        <h5 class="fw-bold text-white mb-0"><?= htmlspecialchars($en['title']) ?></h5>
                                    </div>
                                </div>
                                <div class="text-white-50 small mb-4">
                                    <div class="mb-1"><i class="bi bi-calendar-event me-2"></i><?= date('M d, Y', strtotime($en['event_date'])) ?></div>
                                    <div><i class="bi bi-geo-alt me-2"></i><?= htmlspecialchars($en['location']) ?></div>
                                </div>
                                
                                <?php if ($en['status'] == 'approved'): ?>
                                    <div class="alert bg-success bg-opacity-10 border-success border-opacity-20 text-success small mb-0 py-2">
                                        <i class="bi bi-info-circle me-2"></i> You are approved to attend!
                                    </div>
                                <?php elseif ($en['status'] == 'pending'): ?>
                                    <div class="alert bg-warning bg-opacity-10 border-warning border-opacity-20 text-warning small mb-0 py-2">
                                        <i class="bi bi-hourglass-split me-2"></i> Application is being reviewed.
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent border-top border-white border-opacity-10 p-4">
                                <a href="<?= BASE_URL ?>events/view.php?id=<?= $en['event_id'] ?>" class="btn btn-outline-light w-100 fw-bold">View Event Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
