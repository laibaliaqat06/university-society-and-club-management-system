
<div class="card glass-card mb-4 overflow-hidden hero-glow" style="height: 250px; border:none;">
    <div class="position-absolute w-100 h-100" style="background: url('<?= BASE_URL ?>assets/img/dashboard_banner.png') center/cover no-repeat; filter: brightness(0.6) saturate(1.2);"></div>
    <div class="card-body position-relative d-flex align-items-end p-5 h-100">
        <div>
            <span class="badge bg-info text-dark px-3 py-2 mb-2">Event Operations</span>
            <h2 class="display-6 fw-bold text-white mb-0">Event Hub</h2>
            <p class="text-white-50 lead mb-0">Orchestrate memorable university experiences.</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Attendance -->
    <div class="col-lg-4 col-12">
        <div class="small-box text-bg-maroon">
            <div class="inner">
                <h3>Attendance</h3>
                <p>Mark & Track</p>
            </div>
            <div class="icon">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <a href="<?= BASE_URL ?>events/manage.php" class="small-box-footer">Track Attendance <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>

    <!-- Enrollments -->
    <?php
    $pendingApps = $pdo->query("SELECT COUNT(*) FROM event_enrollments WHERE status = 'pending'")->fetchColumn();
    ?>
    <div class="col-lg-4 col-12">
        <div class="small-box text-bg-warning">
            <div class="inner text-dark">
                <h3><?= $pendingApps ?></h3>
                <p>Pending Enrollments</p>
            </div>
            <div class="icon">
                <i class="bi bi-person-plus-fill text-dark"></i>
            </div>
            <a href="<?= BASE_URL ?>events/manage.php" class="small-box-footer text-dark">Review Applications <i class="bi bi-arrow-right-circle text-dark"></i></a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-teal text-white">
        <h3 class="card-title">Event Operations</h3>
    </div>
    <div class="card-body">
        <p>Ensure smooth execution of all club events.</p>
    </div>
</div>
