<?php
require_once 'core/Announcements.php';
require_once 'core/Events.php';
require_once 'core/Clubs.php';
require_once 'core/Certificates.php';

$annObj = new Announcements($pdo);
$eventsObj = new Events($pdo);
$clubsObj = new Clubs($pdo);
$certObj = new Certificates($pdo);

// Handle RSVP if posted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rsvp_event'])) {
    $eventsObj->rsvp($_POST['event_id'], $_SESSION['user_id'], 'going');
    $msg = "Successfully registered for the event!";
}

$myAnnouncements = array_slice($annObj->getVisibleAnnouncements($_SESSION['user_id'], $_SESSION['role']), 0, 3);
$upcomingEvents = array_slice($eventsObj->getAllUpcoming(), 0, 3);
$discoverClubs = array_slice($clubsObj->getAll(), 0, 3);

// Fetch My Certificates
$myCertsStmt = $pdo->prepare("SELECT c.*, e.title as event_name, cl.name as society_name 
                            FROM certificates c 
                            JOIN events e ON c.event_id = e.id 
                            LEFT JOIN clubs cl ON e.club_id = cl.id 
                            WHERE c.user_id = ? 
                            ORDER BY c.id DESC");
$myCertsStmt->execute([$_SESSION['user_id']]);
$myCertificates = $myCertsStmt->fetchAll();
?>

<?php 
$memberCover = !empty($memberCover) ? $memberCover : BASE_URL . 'assets/img/dashboard_banner.png';
$bgStyle = "background: url('{$memberCover}') center/cover no-repeat; filter: brightness(0.6) saturate(1.2);";
?>
<div class="card glass-card mb-4 overflow-hidden border-0" style="height: 220px;">
    <div class="position-absolute w-100 h-100" style="<?= $bgStyle ?>"></div>
    <div class="card-body position-relative d-flex align-items-center p-5 h-100">
        <div>
            <span class="badge bg-light text-primary px-3 py-2 mb-3 rounded-pill shadow-sm">Student Life Portal</span>
            <h1 class="display-5 fw-bold text-white mb-2">Welcome Back, <?= htmlspecialchars($_SESSION['name']) ?></h1>
            <p class="text-white-50 lead mb-0">Explore, engage, and excel in your campus community.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Active Memberships -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-people-fill text-success fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $pdo->query("SELECT COUNT(*) FROM club_memberships WHERE user_id = " . $_SESSION['user_id'])->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">My Societies</p>
            </div>
            <a href="<?= BASE_URL ?>clubs/mysociety.php" class="stretched-link"></a>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-calendar-event text-primary fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">Global Events</p>
            </div>
            <a href="<?= BASE_URL ?>events/index.php" class="stretched-link"></a>
        </div>
    </div>
    
    <!-- My Enrollments -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-journal-check text-warning fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $pdo->query("SELECT COUNT(*) FROM event_enrollments WHERE user_id = " . $_SESSION['user_id'])->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">My Enrollments</p>
            </div>
            <a href="<?= BASE_URL ?>events/my_enrollments.php" class="stretched-link"></a>
        </div>
    </div>

    <!-- Volunteer Opportunities -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-hand-index-thumb text-info fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $pdo->query("SELECT COUNT(*) FROM events WHERE volunteers_needed > 0 AND event_date >= CURDATE()")->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">Volunteer Work</p>
            </div>
            <a href="<?= BASE_URL ?>events/volunteers.php" class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Discover Societies -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-compass me-2 text-primary"></i> Discover Societies</h4>
            <a href="<?= BASE_URL ?>clubs/index.php" class="btn btn-sm btn-outline-secondary rounded-pill">View All</a>
        </div>
        <div class="row g-3">
            <?php foreach($discoverClubs as $club): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card glass-card h-100 border-0 p-3">
                        <div class="d-flex align-items-center mb-3">
                            <?php $logo = !empty($club['logo']) ? BASE_URL . $club['logo'] : BASE_URL . 'assets/img/default-logo.png'; ?>
                            <img src="<?= $logo ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                            <h6 class="mb-0 fw-bold text-truncate"><?= htmlspecialchars($club['name']) ?></h6>
                        </div>
                        <p class="text-muted x-small mb-3 line-clamp-2"><?= htmlspecialchars($club['description']) ?></p>
                        <a href="<?= BASE_URL ?>clubs/view.php?id=<?= $club['id'] ?>" class="btn btn-sm btn-outline-primary w-100">Explore</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
            <h4 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2 text-warning"></i> Upcoming Events</h4>
            <a href="<?= BASE_URL ?>events/index.php" class="btn btn-sm btn-outline-secondary rounded-pill">View All</a>
        </div>
        <div class="row g-3">
            <?php foreach($upcomingEvents as $event): ?>
                <div class="col-md-12">
                    <div class="card glass-card border-0 p-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-2 text-center" style="min-width: 60px;">
                                    <span class="d-block fw-bold"><?= date('d', strtotime($event['event_date'])) ?></span>
                                    <span class="x-small"><?= date('M', strtotime($event['event_date'])) ?></span>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($event['title']) ?></h6>
                                <p class="text-muted x-small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($event['location']) ?> 
                                    <span class="mx-2">|</span> 
                                    <i class="bi bi-building me-1"></i> <?= htmlspecialchars($event['club_name']) ?>
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="<?= BASE_URL ?>events/view.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-premium rounded-pill px-4">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Announcements & Certificates -->
    <div class="col-lg-4">
        <h4 class="fw-bold mb-4"><i class="bi bi-megaphone me-2 text-info"></i> Latest News</h4>
        <div class="glass-card p-4 mb-5">
            <?php if(empty($myAnnouncements)): ?>
                <p class="text-muted small">No recent announcements.</p>
            <?php else: ?>
                <?php foreach($myAnnouncements as $ann): ?>
                    <div class="mb-4 last-child-mb-0 border-bottom border-secondary border-opacity-10 pb-3">
                        <span class="badge bg-info bg-opacity-10 text-info x-small mb-2">
                             <?= empty($ann['society_name']) ? 'Global' : htmlspecialchars($ann['society_name']) ?>
                        </span>
                        <h6 class="fw-bold mb-2"><?= htmlspecialchars($ann['title']) ?></h6>
                        <p class="text-muted x-small mb-0"><?= substr(htmlspecialchars($ann['message']), 0, 100) ?>...</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h4 class="fw-bold mb-4"><i class="bi bi-patch-check me-2 text-success"></i> My Certificates</h4>
        <div class="glass-card p-4">
            <?php if(empty($myCertificates)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-award text-muted display-6 mb-2"></i>
                    <p class="text-muted small">No certificates earned yet. Attend events to earn them!</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach($myCertificates as $cert): ?>
                        <a href="<?= BASE_URL ?>certificates/generate.php?event_id=<?= $cert['event_id'] ?>&user_id=<?= $_SESSION['user_id'] ?>" target="_blank" class="list-group-item bg-transparent border-secondary border-opacity-10 px-0 py-3 d-flex align-items-center text-decoration-none">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 x-small fw-bold"><?= htmlspecialchars($cert['event_name']) ?></h6>
                                <p class="text-muted extra-small mb-0"><?= htmlspecialchars($cert['society_name']) ?></p>
                            </div>
                            <i class="bi bi-download ms-auto text-muted small"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .last-child-mb-0:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
