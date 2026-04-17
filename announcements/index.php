<?php
require_once '../includes/header.php';
require_once '../core/Announcements.php';

$annObj = new Announcements($pdo);

// Role specific fetching
$role = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? 0;

$announcements = [];
if ($role === 'super_admin' || $role === 'society_admin' || $role === 'member' || $role === 'student') {
    $announcements = $annObj->getVisibleAnnouncements($userId, $role);
}

// Ensure the link is accessible
if (in_array($role, ['guest'])) {
    die('<div class="alert alert-danger m-5">Access Denied</div>');
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: #1e1b4b url('https://images.unsplash.com/photo-1577563908411-50cb98976fea?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2); z-index: 0;"></div>
    <div class="container position-relative py-5" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-3">Notice Board</span>
                <h1 class="display-3 fw-bold text-white mb-3">Announcements</h1>
                <p class="lead text-white-50 mb-0">Stay up to date with the latest campus and society news.</p>
            </div>
            <?php if(in_array($role, ['super_admin', 'society_admin'])): ?>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <a href="create.php" class="btn btn-premium btn-lg shadow-lg px-5">
                    <i class="bi bi-megaphone-fill me-2"></i> Post Announcement
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <?php if(empty($announcements)): ?>
            <div class="glass-card p-5 text-center border-0 shadow-lg">
                <div class="display-1 text-white-50 mb-4"><i class="bi bi-balloon-heart"></i></div>
                <h3 class="fw-bold text-white">All caught up!</h3>
                <p class="text-white-50">No new announcements to show right now. Check back later.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($announcements as $a): 
                    // Category Mapping
                    $config = match($a['type']) {
                        'urgent' => ['badge' => 'danger', 'icon' => 'exclamation-circle-fill', 'bg' => 'rgba(220, 53, 69, 0.05)'],
                        'success' => ['badge' => 'success', 'icon' => 'check-circle-fill', 'bg' => 'rgba(25, 135, 84, 0.05)'],
                        'event' => ['badge' => 'primary', 'icon' => 'calendar-event-fill', 'bg' => 'rgba(13, 110, 253, 0.05)'],
                        default => ['badge' => 'info', 'icon' => 'info-circle-fill', 'bg' => 'rgba(13, 202, 240, 0.05)']
                    };
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card glass-card h-100 border-0 shadow-sm hover-elevate overflow-hidden" style="background: linear-gradient(145deg, <?= $config['bg'] ?>, rgba(0,0,0,0));">
                            <?php if($a['is_pinned']): ?>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-warning text-dark rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-pin-angle-fill me-1"></i> Pinned
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <div class="mb-3">
                                    <span class="badge bg-<?= $config['badge'] ?> bg-opacity-10 text-<?= $config['badge'] ?> px-3 py-2 rounded-3 border border-<?= $config['badge'] ?> border-opacity-25">
                                        <i class="bi bi-<?= $config['icon'] ?> me-2"></i>
                                        <?= empty($a['society_name']) ? 'Global' : htmlspecialchars($a['society_name']) ?>
                                    </span>
                                </div>

                                <h4 class="card-title fw-bold text-white mb-3" style="line-height: 1.4;"><?= htmlspecialchars($a['title']) ?></h4>
                                <p class="card-text text-white-50 mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                                    <?= nl2br(htmlspecialchars($a['message'])) ?>
                                </p>
                                
                                <div class="mt-auto pt-4 border-top border-white border-opacity-10 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-10 text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; border: 1px solid rgba(255,255,255,0.1);">
                                            <i class="bi bi-person small"></i>
                                        </div>
                                        <div class="lh-1">
                                            <small class="text-white d-block fw-bold" style="font-size: 0.8rem;"><?= htmlspecialchars($a['author_name'] ?? 'System') ?></small>
                                            <small class="text-white-50" style="font-size: 0.7rem;"><?= empty($a['society_name']) ? 'Admin' : 'Society Head' ?></small>
                                        </div>
                                    </div>
                                    <small class="text-white-50" style="font-size: 0.75rem;">
                                        <i class="bi bi-calendar3 me-1"></i> <?= date('M d', strtotime($a['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
