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
            <a href="<?= BASE_URL ?>modules/users/index.php" class="stretched-link"></a>
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
            <a href="<?= BASE_URL ?>modules/clubs/index.php" class="stretched-link"></a>
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
                    <a href="<?= BASE_URL ?>modules/dashboards/super_admin/review_events.php" class="btn btn-sm btn-outline-danger rounded-pill">
                        Phase 1: Admin Reviews (<?= $pdo->query("SELECT COUNT(*) FROM events WHERE admin_status = 'pending'")->fetchColumn() ?>)
                    </a>
                    <a href="<?= BASE_URL ?>modules/finance/approve_events.php" class="btn btn-sm btn-outline-success rounded-pill">
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
            <a href="<?= BASE_URL ?>modules/finance/index.php" class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- User Distribution Chart -->
    <div class="col-lg-6">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header border-bottom border-white border-opacity-10 py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i> User Distribution</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-4">
                <div style="width: 100%; max-width: 300px;">
                    <canvas id="userDistChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Growth Chart -->
    <div class="col-lg-6">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header border-bottom border-white border-opacity-10 py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-success"></i> Monthly Event Activity</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="eventGrowthChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Fetch User Distribution Data
$roleCounts = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll();
$roleLabels = [];
$roleData = [];
foreach($roleCounts as $rc) {
    $roleLabels[] = ucfirst(str_replace('_', ' ', $rc['role'] ?: 'Unassigned'));
    $roleData[] = $rc['count'];
}

// Fetch Monthly Event Data
$monthlyEvents = $pdo->query("SELECT DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count FROM events GROUP BY month ORDER BY created_at ASC LIMIT 6")->fetchAll();
$monthLabels = [];
$monthData = [];
foreach($monthlyEvents as $me) {
    $monthLabels[] = $me['month'];
    $monthData[] = $me['count'];
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Distribution Chart
    new Chart(document.getElementById('userDistChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($roleLabels) ?>,
            datasets: [{
                data: <?= json_encode($roleData) ?>,
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#ffffff90', font: { size: 10 } } }
            },
            cutout: '70%'
        }
    });

    // Event Growth Chart
    new Chart(document.getElementById('eventGrowthChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($monthLabels) ?>,
            datasets: [{
                label: 'Events Created',
                data: <?= json_encode($monthData) ?>,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#ffffff10' }, ticks: { color: '#ffffff50' } },
                x: { grid: { display: false }, ticks: { color: '#ffffff50' } }
            }
        }
    });
});
</script>

<?php include 'analytics.php'; ?>

