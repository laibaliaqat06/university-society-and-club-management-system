
<?php
// Get the club managed by this user
$stmt = $pdo->prepare("SELECT * FROM clubs WHERE created_by = ?");
$stmt->execute([$_SESSION['user_id']]);
$myClub = $stmt->fetch();
$clubId = $myClub['id'] ?? 0;
?>
<?php if($myClub): ?>
<div class="card glass-card mb-4 overflow-hidden hero-glow" style="height: 250px; border:none;">
    <?php 
        $cover = !empty($myClub['cover_image']) ? $myClub['cover_image'] : BASE_URL . 'assets/img/dashboard_banner.png'; 
        if (!empty($myClub['cover_image']) && strpos($myClub['cover_image'], 'http') !== 0) {
            $cover = BASE_URL . $myClub['cover_image'];
        }
        $logo = !empty($myClub['logo']) ? $myClub['logo'] : BASE_URL.'assets/img/avatar.png';
        if (!empty($myClub['logo']) && strpos($myClub['logo'], 'http') !== 0) {
            $logo = BASE_URL . $myClub['logo'];
        }
    ?>
    <div class="position-absolute w-100 h-100" style="background: url('<?= $cover ?>') center/cover no-repeat; filter: brightness(0.4) saturate(1.2);"></div>
    <div class="card-body position-relative d-flex align-items-end p-5 h-100">
        <div class="d-flex align-items-center">
            <img src="<?= $logo ?>" class="rounded-circle border border-white border-4 shadow me-4" style="width: 100px; height: 100px; object-fit: cover; background: #fff;">
            <div>
                <span class="badge bg-warning text-dark px-3 py-2 mb-2">Society Management</span>
                <h2 class="display-6 fw-bold text-white mb-0"><?= htmlspecialchars($myClub['name']) ?></h2>
                <p class="text-white-50 lead mb-0"><?= htmlspecialchars($myClub['category']) ?> • Hub of Excellence</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- My Managed Society -->
    <div class="col-lg-6 col-12">
        <div class="small-box text-bg-purple"> 
            <div class="inner">
                <?php if($myClub): ?>
                    <h3><?= htmlspecialchars($myClub['name']) ?></h3>
                    <p><?= $pdo->query("SELECT COUNT(*) FROM club_memberships WHERE club_id = $clubId")->fetchColumn() ?> Members</p>
                <?php else: ?>
                    <h3>No Club</h3>
                    <p>You haven't created a club yet.</p>
                <?php endif; ?>
            </div>
            <div class="icon">
                <i class="bi bi-building-fill-gear"></i>
            </div>
            <a href="<?= BASE_URL ?>clubs/mysociety.php" class="small-box-footer">Manage <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>

    <!-- Event Requests -->
    <div class="col-lg-6 col-12">
        <div class="small-box text-bg-orange">
            <div class="inner">
                <h3><?= $pdo->query("SELECT COUNT(*) FROM events WHERE club_id = $clubId")->fetchColumn() ?></h3>
                <p>Events Organized</p>
            </div>
            <div class="icon">
                <i class="bi bi-calendar-plus-fill"></i>
            </div>
            <a href="<?= BASE_URL ?>events/manage.php" class="small-box-footer">Manage Events <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>
</div>

<div class="card card-outline card-warning mt-4">
    <div class="card-header">
        <h3 class="card-title">Society Administration</h3>
    </div>
    <div class="card-body">
        <h5>Responsibilities:</h5>
        <ul>
            <li>Approve member requests.</li>
            <li>Post announcements and <a href="<?= BASE_URL ?>events/manage.php" class="text-warning fw-bold">Event Gallery</a> images.</li>
            <li>Organize events for your society.</li>
        </ul>
    </div>
</div>

<?php include 'analytics.php'; ?>
