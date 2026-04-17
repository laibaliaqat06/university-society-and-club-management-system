<div class="card glass-card mb-4 overflow-hidden border-0" style="height: 220px;">
    <div class="position-absolute w-100 h-100" style="background: url('<?= BASE_URL ?>assets/img/dashboard_banner.png') center/cover no-repeat; filter: brightness(0.6) saturate(1.2);"></div>
    <div class="card-body position-relative d-flex align-items-center p-5 h-100">
        <div>
            <span class="badge bg-primary px-3 py-2 mb-3 rounded-pill">System Overview</span>
            <h1 class="display-5 fw-bold text-white mb-2">Super Admin Dashboard</h1>
            <p class="text-white-50 lead mb-0">Oversee global campus activity and system infrastructure.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-people-fill text-warning fs-3"></i>
                    </div>
                    <span class="text-success small fw-bold">+12% <i class="bi bi-arrow-up"></i></span>
                </div>
                <h3 class="fw-bold mb-1 text-white"><?= $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">Total Active Users</p>
            </div>
            <a href="<?= BASE_URL ?>users/index.php" class="stretched-link"></a>
        </div>
    </div>

    <!-- Total Clubs -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-collection-fill text-primary fs-3"></i>
                    </div>
                    <span class="text-primary small fw-bold">Live</span>
                </div>
                <h3 class="fw-bold mb-1 text-white"><?= $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">Societies & Clubs</p>
            </div>
            <a href="<?= BASE_URL ?>clubs/index.php" class="stretched-link"></a>
        </div>
    </div>
    
    <!-- Total Events -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-calendar-event-fill text-danger fs-3"></i>
                    </div>
                    <span class="text-danger small fw-bold">Upcoming</span>
                </div>
                <h3 class="fw-bold mb-1 text-white"><?= $pdo->query("SELECT COUNT(*) FROM events WHERE admin_status = 'approved' AND finance_status = 'approved'")->fetchColumn() ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">Total Campus Events</p>
                <div class="mt-3 d-grid gap-2">
                    <a href="<?= BASE_URL ?>dashboards/super_admin/review_events.php" class="btn btn-sm btn-outline-danger rounded-pill">
                        Phase 1: Admin Reviews (<?= $pdo->query("SELECT COUNT(*) FROM events WHERE admin_status = 'pending'")->fetchColumn() ?>)
                    </a>
                    <a href="<?= BASE_URL ?>finance/approve_events.php" class="btn btn-sm btn-outline-success rounded-pill">
                        Phase 2: Budget Reviews (<?= $pdo->query("SELECT COUNT(*) FROM events WHERE admin_status = 'approved' AND finance_status = 'pending'")->fetchColumn() ?>)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Finance Overview -->
    <div class="col-xl-3 col-md-6">
        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4">
                        <i class="bi bi-cash-coin text-success fs-3"></i>
                    </div>
                    <span class="text-success small fw-bold">Safe</span>
                </div>
                <h3 class="fw-bold mb-1 text-white">Rs <?= number_format($pdo->query("SELECT SUM(CASE WHEN type='income' THEN amount ELSE -amount END) FROM finance_records WHERE status='approved'")->fetchColumn() ?? 0, 0) ?></h3>
                <p class="text-muted small fw-bold text-uppercase mb-0">Global Treasury</p>
            </div>
            <a href="<?= BASE_URL ?>finance/index.php" class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="card glass-card mt-4">
    <div class="card-header border-bottom border-white border-opacity-10">
        <h3 class="card-title">System Overview</h3>
    </div>
    <div class="card-body">
        <p class="text-white-50">Welcome, Super Admin. You have full control over the system.</p>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Manage Users and assign roles.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Create and oversee Societies.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Approve Major Events.</li>
                    <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> Configure System Settings.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'analytics.php'; ?>
