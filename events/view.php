<?php
require_once '../core/session.php';
require_once '../core/Events.php';

$eventId = $_GET['id'] ?? 0;
$eventsObj = new Events($pdo);
$event = $eventsObj->getById($eventId);

if (!$event) {
    require_once '../includes/header.php';
    echo "<div class='alert alert-danger'>Event not found.</div>";
    require_once '../includes/footer.php';
    exit;
}

// Visibility Guard: Only show approved events to public. 
// Admins or the creator can see pending/rejected ones.
$isAuthorized = ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'finance_manager' || $_SESSION['user_id'] == $event['created_by']);
$isApproved = ($event['admin_status'] === 'approved' && $event['finance_status'] === 'approved');

if (!$isApproved && !$isAuthorized) {
    require_once '../includes/header.php';
    echo "<div class='container mt-5'><div class='alert alert-warning text-center p-5'>
            <i class='bi bi-shield-lock display-4 d-block mb-3'></i>
            <h3>Approval Pending</h3>
            <p>This event proposal is currently undergoing administrative and financial review.</p>
            <a href='index.php' class='btn btn-primary mt-3'>Back to Events</a>
          </div></div>";
    require_once '../includes/footer.php';
    exit;
}

// Handle Enrollment
if (isset($_POST['enroll_event']) && isset($_SESSION['user_id'])) {
    $enrollData = [
        'name' => $_POST['student_name'],
        'email' => $_POST['student_email'],
        'phone' => $_POST['student_phone'],
        'message' => $_POST['message']
    ];
    if ($eventsObj->enroll($eventId, $_SESSION['user_id'], $enrollData)) {
        header("Location: view.php?id=" . $eventId . "&msg=Enrollment Application Submitted");
        exit;
    }
}

require_once '../includes/header.php';

// Get user's current enrollment status
$userEnrollment = null;
if (isset($_SESSION['user_id'])) {
    $userEnrollment = $eventsObj->getUserEnrollment($eventId, $_SESSION['user_id']);
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5 text-center">
        <span class="badge bg-primary px-3 py-2 mb-3"><?= htmlspecialchars($event['club_name']) ?></span>
        <h1 class="display-3 fw-bold text-white mb-3"><?= htmlspecialchars($event['title']) ?></h1>
        <p class="lead text-white-50 mb-0">Hosted by <span class="text-white"><?= htmlspecialchars($event['creator_name']) ?></span></p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Event Info -->
        <div class="col-lg-8">
            <div class="card glass-card border-0 shadow-lg p-4 mb-4">
                <h3 class="fw-bold mb-4">About the Event</h3>
                <p class="lead text-secondary"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                
                <hr class="my-4 border-white border-opacity-10">
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-3 p-3 me-3">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block uppercase small fw-bold">Location</small>
                                <span class="fw-bold"><?= htmlspecialchars($event['location']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                                <i class="bi bi-calendar-event-fill fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block uppercase small fw-bold">Date & Time</small>
                                <span class="fw-bold"><?= date('F d, Y - h:i A', strtotime($event['event_date'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="index.php" class="btn btn-outline-secondary px-4 mt-4">
                <i class="bi bi-arrow-left me-2"></i> Back to Events
            </a>

            <?php 
            // Fetch Sponsors
            $sStmt = $pdo->prepare("SELECT * FROM event_sponsors WHERE event_id = ? ORDER BY contribution_amount DESC");
            $sStmt->execute([$eventId]);
            $sponsors = $sStmt->fetchAll();
            if (!empty($sponsors)):
            ?>
            <div class="mt-5 mb-4">
                <h3 class="fw-bold mb-4"><i class="bi bi-star-fill text-warning me-2"></i> Event Sponsors</h3>
                <div class="row g-3">
                    <?php foreach ($sponsors as $s): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card glass-card border-0 shadow-sm h-100 text-center p-3">
                            <?php if (!empty($s['logo_path']) && file_exists('../' . $s['logo_path'])): ?>
                                <img src="../<?= htmlspecialchars($s['logo_path']) ?>" alt="<?= htmlspecialchars($s['sponsor_name']) ?>" class="img-fluid mx-auto d-block mb-3 rounded" style="max-height: 80px; object-fit: contain;">
                            <?php else: ?>
                                <div class="bg-secondary bg-opacity-10 text-secondary rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                                    <i class="bi bi-building"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="fw-bold mb-1"><?= htmlspecialchars($s['sponsor_name']) ?></h5>
                            <?php if ($s['contribution_amount'] > 0): ?>
                            <span class="badge bg-success bg-opacity-25 text-success">Official Partner</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php 
            if ($event['volunteers_needed'] > 0): 
                $vStmt = $pdo->prepare("SELECT a.*, u.name as volunteer_name FROM event_volunteer_apps a JOIN users u ON a.user_id = u.id WHERE a.event_id = ? AND a.status = 'selected'");
                $vStmt->execute([$eventId]);
                $volunteers = $vStmt->fetchAll();
            ?>
            <div class="mt-5 mb-4 p-4 rounded-4" style="background: rgba(var(--bs-success-rgb), 0.05); border: 1px solid rgba(var(--bs-success-rgb), 0.1);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0"><i class="bi bi-people-fill text-success me-2"></i> Volunteer Roster</h3>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">
                        <?= count($volunteers) ?> / <?= $event['volunteers_needed'] ?> Spots Filled
                    </span>
                </div>
                
                <?php if (empty($volunteers)): ?>
                    <p class="text-muted mb-0">No volunteers have been selected yet.</p>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($volunteers as $v): ?>
                            <span class="badge bg-light text-dark border p-2 shadow-sm rounded-pill"><i class="bi bi-person-check-fill text-success me-1"></i> <?= htmlspecialchars($v['volunteer_name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_admin', 'society_admin', 'event_manager'])): ?>
                    <div class="mt-4 pt-3 border-top">
                        <p class="small text-muted mb-2">Want to help out?</p>
                        <a href="volunteers.php" class="btn btn-sm btn-outline-success">View Volunteer Opportunities</a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php 
            $gallery = $eventsObj->getGallery($eventId); 
            if (!empty($gallery)): 
            ?>
            <div class="mt-5">
                <h3 class="fw-bold mb-4">📸 Event Gallery & Highlights</h3>
                <div class="row g-3">
                    <?php foreach ($gallery as $item): 
                        $mediaSrc = $item['image'];
                        if (strpos($mediaSrc, 'http') !== 0) {
                            $mediaSrc = BASE_URL . $mediaSrc;
                        }
                    ?>
                        <div class="col-md-4 col-6">
                            <div class="gallery-card rounded-4 overflow-hidden shadow-sm bg-dark">
                                <?php if ($item['media_type'] === 'video'): ?>
                                    <div class="ratio ratio-16x9">
                                        <video controls class="w-100 h-100">
                                            <source src="<?= $mediaSrc ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                <?php else: ?>
                                    <a href="<?= $mediaSrc ?>" target="_blank">
                                        <img src="<?= $mediaSrc ?>" class="img-fluid w-100" style="height: 200px; object-fit: cover;" alt="Event Photo">
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <style>
            .gallery-card {
                transition: transform 0.3s ease;
                border: 1px solid rgba(255,255,255,0.05);
            }
            .gallery-card:hover {
                transform: scale(1.02);
                border-color: rgba(255,255,255,0.2);
            }
            .gallery-card img {
                filter: brightness(0.9);
                transition: 0.3s;
            }
            .gallery-card:hover img {
                filter: brightness(1.1);
            }
            </style>
            <?php endif; ?>
        </div>
        
        <!-- Registration/Enrollment Side Module -->
        <div class="col-lg-4">
            <div class="card glass-card border-0 shadow-lg p-4 sticky-top" style="top: 100px;">
                <h4 class="fw-bold mb-3">Enroll in Event</h4>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($userEnrollment): ?>
                        <div class="text-center py-4">
                            <div class="icon-circle bg-<?= $userEnrollment['status'] == 'approved' ? 'success' : ($userEnrollment['status'] == 'rejected' ? 'danger' : 'warning') ?> text-white mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px; border-radius: 50%; font-size: 24px;">
                                <i class="bi bi-<?= $userEnrollment['status'] == 'approved' ? 'check-lg' : ($userEnrollment['status'] == 'rejected' ? 'x-lg' : 'clock-history') ?>"></i>
                            </div>
                            <h5 class="fw-bold">Application <?= ucfirst($userEnrollment['status']) ?></h5>
                            <p class="text-muted small">
                                <?php if ($userEnrollment['status'] == 'pending'): ?>
                                    Your application has been received and is waiting for review by the event organizers.
                                <?php elseif ($userEnrollment['status'] == 'approved'): ?>
                                    Congratulations! You are officially enrolled in this event.
                                <?php else: ?>
                                    Sorry, your application for this event was not approved.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-4">Complete the form below to apply for participation in this event.</p>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Name</label>
                                <input type="text" name="student_name" class="form-control" value="<?= htmlspecialchars($_SESSION['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="student_email" class="form-control" value="<?= htmlspecialchars($_SESSION['email']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Phone Number</label>
                                <input type="text" name="student_phone" class="form-control" placeholder="+92 300 1234567" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Message to Organizer</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Explain why you want to participate..."></textarea>
                            </div>
                            <button type="submit" name="enroll_event" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Submit Application
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-success mt-3 small py-2"><?= htmlspecialchars($_GET['msg']) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-3">
                        <p class="mb-3">Please log in to enroll for this event.</p>
                        <a href="<?= BASE_URL ?>login.php" class="btn btn-primary w-100">Login Now</a>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 pt-4 border-top border-white border-opacity-10 text-center">
                    <p class="small text-muted mb-2">Organized by</p>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($event['club_name']) ?></h5>
                    <a href="<?= BASE_URL ?>clubs/view.php?id=<?= $event['club_id'] ?>" class="btn btn-link btn-sm text-primary">View Club Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<?php require_once '../includes/footer.php'; ?>
