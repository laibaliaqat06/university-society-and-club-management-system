<?php
// guest.php - Modern Landing Page for New Students
$upcomingStmt = $pdo->query("SELECT e.*, c.name as club_name, c.logo 
                            FROM events e 
                            JOIN clubs c ON e.club_id = c.id 
                            WHERE e.event_date >= CURDATE() 
                            ORDER BY e.event_date ASC LIMIT 3");
$publicEvents = $upcomingStmt->fetchAll();

$clubsStmt = $pdo->query("SELECT * FROM clubs ORDER BY name ASC LIMIT 6");
$allClubs = $clubsStmt->fetchAll();
?>

<style>
    .hero-section {
        background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.15), transparent),
                    radial-gradient(circle at bottom left, rgba(6, 182, 212, 0.15), transparent);
        border-radius: 30px;
        padding: 100px 40px;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        box-shadow: 0 20px 50px -20px rgba(0, 0, 0, 0.3);
    }

    .hero-title {
        font-size: 3.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.1;
        letter-spacing: -2px;
    }

    .stat-box {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 25px;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .stat-box:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.05);
    }

    .event-card-mini {
        transition: all 0.3s ease;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .event-card-mini:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: scale(1.02);
    }

    .floating-icons i {
        position: absolute;
        opacity: 0.1;
        color: #6366f1;
        z-index: 0;
    }
</style>

<div class="app-content">
    <div class="container-fluid">
        <!-- Hero Section -->
        <div class="hero-section text-center position-relative">
            <div class="floating-icons">
                <i class="bi bi-stars" style="top: 10%; left: 5%; font-size: 3rem;"></i>
                <i class="bi bi-lightning-charge" style="bottom: 15%; right: 10%; font-size: 4rem;"></i>
                <i class="bi bi-trophy" style="top: 20%; right: 5%; font-size: 2.5rem;"></i>
            </div>
            
            <div class="position-relative" style="z-index: 1;">
                <span class="badge bg-premium px-3 py-2 mb-3 rounded-pill">Welcome to Universal Society Hub</span>
                <h1 class="hero-title mb-4">Unleash Your Potential <br> Beyond any Classroom.</h1>
                <p class="lead text-white-50 mb-5 mx-auto" style="max-width: 700px;">
                    Join over 2,000+ students in our vibrant community. Explore societies, lead events, and build a legacy that matters.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= BASE_URL ?>modules/register.php" class="btn btn-premium btn-lg px-5 py-3 fw-bold rounded-pill">
                        Join the Community <i class="bi bi-arrow-right-short ms-2"></i>
                    </a>
                    <a href="<?= BASE_URL ?>modules/login.php" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold rounded-pill">
                        Member Login
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-box">
                    <h2 class="fw-bold text-primary mb-0"><?= $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn() ?>+</h2>
                    <p class="text-white-50 mb-0 small">Active Societies</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h2 class="fw-bold text-warning mb-0"><?= $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn() ?>+</h2>
                    <p class="text-white-50 mb-0 small">Upcoming Events</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h2 class="fw-bold text-success mb-0"><?= $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?>+</h2>
                    <p class="text-white-50 mb-0 small">Engaged Students</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h2 class="fw-bold text-info mb-0"><?= $pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn() ?>+</h2>
                    <p class="text-white-50 mb-0 small">Awards Granted</p>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <!-- Events Preview -->
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-white mb-0">Experience the Excitement</h4>
                    <span class="text-white-50 small">Sneak peek of upcoming events</span>
                </div>
                
                <?php if($publicEvents): ?>
                    <?php foreach($publicEvents as $e): ?>
                        <div class="card event-card-mini p-3 mb-3 border-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 text-center" style="min-width: 70px;">
                                        <h4 class="mb-0 fw-bold"><?= date('d', strtotime($e['event_date'])) ?></h4>
                                        <small><?= date('M', strtotime($e['event_date'])) ?></small>
                                    </div>
                                </div>
                                <div class="col">
                                    <h6 class="text-white fw-bold mb-1"><?= htmlspecialchars($e['title']) ?></h6>
                                    <p class="text-white-50 x-small mb-0">
                                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($e['location']) ?>
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-building me-1"></i> <?= htmlspecialchars($e['club_name']) ?>
                                    </p>
                                </div>
                                <div class="col-auto">
                                    <a href="<?= BASE_URL ?>modules/login.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-white-50">No upcoming events at the moment. Check back soon!</p>
                <?php endif; ?>
            </div>

            <!-- Society Preview -->
            <div class="col-lg-5">
                <h4 class="fw-bold text-white mb-4">Featured Societies</h4>
                <div class="row g-3">
                    <?php foreach($allClubs as $club): ?>
                        <div class="col-6">
                            <div class="glass-card p-3 h-100 text-center border-0" style="border-radius: 20px;">
                                <img src="<?= !empty($club['logo']) ? BASE_URL . $club['logo'] : BASE_URL . 'assets/img/default-logo.png' ?>" class="rounded-circle mb-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <h6 class="text-white small fw-bold text-truncate mb-0"><?= htmlspecialchars($club['name']) ?></h6>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="text-center py-5 mt-5">
            <h3 class="fw-bold text-white mb-4">Ready to embark on a new journey?</h3>
            <a href="<?= BASE_URL ?>modules/register.php" class="btn btn-premium btn-lg px-5 py-3 rounded-pill fw-bold hvr-grow">Get Started Today for Free</a>
        </div>
    </div>
</div>

