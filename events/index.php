<?php
require_once '../includes/header.php';
require_once '../core/Events.php';

$eventsObj = new Events($pdo);
$upcomingEvents = $eventsObj->getAllUpcoming();
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: #1e1b4b url('<?= BASE_URL ?>assets/img/societies_banner.png') center/cover no-repeat; filter: brightness(0.6) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-3">Campus Events</span>
                <h1 class="display-3 fw-bold text-white mb-3">Upcoming Experiences</h1>
                <p class="lead text-white-50 mb-0">Don't miss out on the pulse of campus. Join workshops, competitions, and socials.</p>
            </div>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <?php if ($_SESSION['role'] === 'super_admin' || $_SESSION['role'] === 'event_manager'): ?>
                    <a href="manage.php" class="btn btn-premium btn-lg shadow-lg px-5">
                        <i class="bi bi-gear-fill me-2"></i> Manage Events
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row g-4">
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="col-xl-4 col-md-6">
                    <div class="card h-100 border-0 shadow-lg bg-dark text-white overflow-hidden pill-container" style="border-radius: 12px; background: #2b3035 !important;">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill bg-primary bg-opacity-25 text-primary px-3 py-2">
                                    <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($event['club_name']) ?>
                                </span>
                                <div class="text-white-50 small text-center bg-white bg-opacity-10 rounded px-2 py-1">
                                    <span class="d-block fw-bold"><?= strtoupper(date('M', strtotime($event['event_date']))) ?></span>
                                    <span class="fs-5"><?= date('d', strtotime($event['event_date'])) ?></span>
                                </div>
                            </div>
                            
                            <h4 class="card-title fw-bold text-white mb-3"><?= htmlspecialchars($event['title']) ?></h4>
                            <p class="card-text text-white-50 small mb-4 flex-grow-1">
                                <?= htmlspecialchars($event['description']) ?>
                            </p>
                            
                            <div class="mt-auto pt-3 border-top border-secondary text-white-50 small">
                                <div class="mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <?= htmlspecialchars($event['location']) ?></div>
                                <div class="mb-3"><i class="bi bi-clock-fill text-info me-2"></i> <?= date('h:i A', strtotime($event['event_date'])) ?></div>
                                <a href="<?= BASE_URL ?>events/view.php?id=<?= $event['id'] ?>" class="btn btn-outline-light w-100 rounded-3">Get Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($upcomingEvents)): ?>
                <div class="col-12 text-center mt-5">
                    <div class="alert bg-dark text-white border-secondary py-5 shadow-sm">
                        <i class="bi bi-calendar-x display-4 text-muted mb-3 d-block"></i>
                        <h5 class="fw-bold">No Upcoming Events</h5>
                        <p class="text-muted">Stay tuned for more exciting events coming soon!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="row g-4 mt-5" id="past-events">
            <div class="col-12">
                <h2 class="fw-bold mb-4 text-white">⏪ Memories from Past Events</h2>
                <p class="text-white-50">Relive the best moments from our previous university activities.</p>
            </div>
            <?php 
            $pastEvents = $eventsObj->getPastEvents();
            if (empty($pastEvents)):
            ?>
                <div class="col-12 text-center py-4">
                    <div class="alert bg-dark text-white border-secondary py-4">
                        <p class="mb-0 text-white-50 small">No past events recorded yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($pastEvents as $event): 
                    $galleryCount = count($eventsObj->getGallery($event['id']));
                ?>
                    <div class="col-xl-3 col-md-6">
                        <div class="card h-100 border-0 shadow-sm bg-dark text-white overflow-hidden" style="border-radius: 12px; background: #212529 !important; border: 1px solid rgba(255,255,255,0.05) !important;">
                            <div class="card-body p-3 d-flex flex-column">
                                <span class="badge rounded-pill bg-secondary bg-opacity-25 text-white-50 px-2 py-1 mb-2 align-self-start small">
                                    <?= htmlspecialchars($event['club_name']) ?>
                                </span>
                                <h5 class="card-title fw-bold text-white mb-2"><?= htmlspecialchars($event['title']) ?></h5>
                                <div class="text-white-50 small mb-3">
                                    <i class="bi bi-calendar-check me-2"></i> <?= date('M d, Y', strtotime($event['event_date'])) ?>
                                </div>
                                <?php if ($galleryCount > 0): ?>
                                    <div class="mb-3">
                                        <span class="badge bg-warning text-dark"><i class="bi bi-images me-1"></i> <?= $galleryCount ?> Photos</span>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>events/view.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-light w-100 rounded-pill mt-auto">View Gallery</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .pill-container .badge {
        letter-spacing: 0.5px;
        font-weight: 500;
    }
    .card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
        box-shadow: 0 1rem 3rem rgba(0,0,0,.5)!important;
    }
</style>

<?php require_once '../includes/footer.php'; ?>
