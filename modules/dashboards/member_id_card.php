<?php
require_once '../../includes/header.php';

// Fetch user details for the ID card
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Generate QR Code data (e.g., student registration number or user ID)
$qrData = $user['registration_no'] ?? $user['id'];
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
?>

<div class="container py-5 d-flex justify-content-center">
    <div class="id-card-container">
        <div class="card id-card border-0 shadow-lg overflow-hidden" style="width: 380px; border-radius: 20px;">
            <div class="id-card-header bg-premium p-4 text-center text-white position-relative">
                <div class="id-card-circle bg-white bg-opacity-10 position-absolute" style="width: 150px; height: 150px; border-radius: 50%; top: -50px; right: -50px;"></div>
                <img src="<?= !empty($settings['system_logo']) ? $settings['system_logo'] : BASE_URL.'assets/img/logo.png' ?>" class="mb-2" style="height: 40px;">
                <h5 class="fw-bold mb-0"><?= $settings['system_name'] ?></h5>
                <p class="small mb-0 opacity-75">Student Identification Card</p>
            </div>
            <div class="id-card-body p-4 text-center bg-body">
                <div class="avatar-container mb-3 position-relative d-inline-block">
                    <?php 
                        $avatar = !empty($user['avatar']) ? $user['avatar'] : BASE_URL.'assets/img/avatar.png';
                        if (!empty($user['avatar']) && strpos($user['avatar'], 'http') !== 0) {
                            $avatar = BASE_URL . $user['avatar'];
                        }
                    ?>
                    <img src="<?= $avatar ?>" class="rounded-circle border border-5 border-white shadow" style="width: 120px; height: 120px; object-fit: cover;">
                    <div class="status-indicator bg-success position-absolute" style="width: 15px; height: 15px; border-radius: 50%; bottom: 10px; right: 10px; border: 2px solid white;"></div>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                <p class="text-primary fw-bold small mb-3"><?= strtoupper(str_replace('_', ' ', $user['role'])) ?></p>
                
                <div class="row g-2 mb-4 text-start bg-light p-3 rounded-3">
                    <div class="col-6">
                        <label class="extra-small text-muted d-block text-uppercase">Registration No</label>
                        <span class="small fw-bold"><?= htmlspecialchars($user['registration_no'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-6">
                        <label class="extra-small text-muted d-block text-uppercase">Identity No</label>
                        <span class="small fw-bold"><?= htmlspecialchars($user['identity_no'] ?? 'N/A') ?></span>
                    </div>
                    <div class="col-12 mt-2">
                        <label class="extra-small text-muted d-block text-uppercase">Email</label>
                        <span class="small fw-bold"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                </div>

                <div class="qr-code-container bg-white p-2 d-inline-block rounded shadow-sm">
                    <img src="<?= $qrCodeUrl ?>" alt="QR Code" style="width: 100px; height: 100px;">
                </div>
                <p class="extra-small text-muted mt-2">Scan for digital verification</p>
            </div>
            <div class="id-card-footer bg-premium py-2 text-center text-white">
                <small class="extra-small"><?= $settings['footer_text'] ?></small>
            </div>
        </div>
        
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 me-2">
                <i class="bi bi-printer me-2"></i> Print ID
            </button>
            <a href="<?= BASE_URL ?>modules/dashboards/member.php" class="btn btn-premium rounded-pill px-4">
                Back to Portal
            </a>
        </div>
    </div>
</div>

<style>
    .bg-premium {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    }
    .extra-small { font-size: 0.65rem; }
    @media print {
        .no-print { display: none !important; }
        .app-header, .app-sidebar, .app-footer, .app-content-header { display: none !important; }
        .app-main { margin: 0 !important; padding: 0 !important; }
        .id-card-container { margin: 0 auto; }
        body { background: white !important; }
    }
</style>

<?php require_once '../../includes/footer.php'; ?>

