<?php 
require_once '../includes/session.php'; // Ensures login
require_once '../includes/header.php'; 

$uID = $_SESSION['user_id'];
$msg = "";
$msgType = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    
    // 1. Handle File Upload
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . "_" . basename($_FILES['avatar']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        // Allow certain file formats
        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array(strtolower($fileType), $allowTypes)){
            if(move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFilePath)){
                // Update DB
                $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$targetFilePath, $uID]);
            }
        }
    }

    // 2. Update Password (if provided)
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $uID]);
    }

    // 3. Update Basic Info
    $pdo->prepare("UPDATE users SET name = ? WHERE id = ?")->execute([$name, $uID]);

    // Refresh Session Name
    $_SESSION['name'] = $name;
    $_SESSION['msg'] = "Profile updated successfully!";
    $_SESSION['msg_type'] = "success";
    header("Location: profile.php");
    exit;
}

// Fetch Latest User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uID]);
$user = $stmt->fetch();
?>

<div class="row g-4 hero-glow">
    <div class="col-lg-4">
        <div class="card glass-card mb-4 text-center p-4 border-0">
            <div class="mb-3">
                <?php 
                    $uAvatar = !empty($user['avatar']) ? $user['avatar'] : 'assets/img/avatar.png';
                    if (strpos($uAvatar, 'http') !== 0) {
                        $uAvatar = BASE_URL . $uAvatar;
                    }
                ?>
                <img class="rounded-circle border border-white border-4 shadow-lg p-1 bg-white"
                     src="<?= $uAvatar ?>"
                     alt="User profile picture" style="width: 150px; height: 150px; object-fit: cover;">
            </div>
            <h3 class="fw-bold text-white mb-1"><?= htmlspecialchars($user['name']) ?></h3>
            <p class="text-primary fw-bold small text-uppercase mb-3"><?= str_replace('_', ' ', $user['role']) ?></p>
            
            <div class="d-flex justify-content-center gap-2 mb-4">
                <a href="<?= BASE_URL ?>modules/dashboards/member_id_card.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                    <i class="bi bi-person-badge me-1"></i> Digital ID
                </a>
            </div>

            <hr class="border-white border-opacity-10">
            <div class="text-start">
                <div class="mb-3">
                    <label class="extra-small text-muted d-block text-uppercase">Email Address</label>
                    <span class="text-white small fw-bold"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="mb-3">
                    <label class="extra-small text-muted d-block text-uppercase">Registration No</label>
                    <span class="text-white small fw-bold"><?= htmlspecialchars($user['registration_no'] ?? 'N/A') ?></span>
                </div>
                <div class="mb-0">
                    <label class="extra-small text-muted d-block text-uppercase">Member Since</label>
                    <span class="text-white small fw-bold"><?= date('F Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>

        <!-- My Societies -->
        <div class="card glass-card border-0 p-4">
            <h5 class="fw-bold text-white mb-4"><i class="bi bi-collection-fill text-primary me-2"></i> My Societies</h5>
            <?php 
            $myClubs = $pdo->prepare("SELECT c.id, c.name, c.logo, m.role FROM club_memberships m JOIN clubs c ON m.club_id = c.id WHERE m.user_id = ? AND m.status = 'approved'");
            $myClubs->execute([$uID]);
            $clubsList = $myClubs->fetchAll();
            if ($clubsList): 
                foreach ($clubsList as $c):
                    $cLogo = !empty($c['logo']) ? BASE_URL . $c['logo'] : BASE_URL . 'assets/img/default-logo.png';
            ?>
                <div class="d-flex align-items-center mb-3">
                    <img src="<?= $cLogo ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="overflow-hidden">
                        <a href="<?= BASE_URL ?>modules/clubs/view.php?id=<?= $c['id'] ?>" class="text-white small fw-bold d-block text-truncate text-decoration-none"><?= htmlspecialchars($c['name']) ?></a>
                        <span class="extra-small text-white-50 text-uppercase"><?= $c['role'] ?></span>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <p class="text-white-50 small text-center py-3">Not a member of any society yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Tabs for Settings & Resume -->
        <div class="card glass-card border-0">
            <div class="card-header border-bottom border-white border-opacity-10 p-0">
                <ul class="nav nav-tabs border-0" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active text-white border-0 py-3 px-4" id="resume-tab" data-bs-toggle="tab" data-bs-target="#resume" type="button">My Portfolio</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link text-white border-0 py-3 px-4" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">Account Settings</button>
                    </li>
                </ul>
            </div>
            <div class="tab-content p-4" id="profileTabContent">
                <!-- Resume/Portfolio Tab -->
                <div class="tab-pane fade show active" id="resume" role="tabpanel">
                    <!-- Certificates -->
                    <h5 class="fw-bold text-white mb-4"><i class="bi bi-award-fill text-warning me-2"></i> Earned Certificates</h5>
                    <div class="row g-3">
                        <?php 
                        $certs = $pdo->prepare("SELECT * FROM certificates WHERE user_id = ? ORDER BY issued_at DESC");
                        $certs->execute([$uID]);
                        $certList = $certs->fetchAll();
                        if ($certList): foreach($certList as $cert): ?>
                            <div class="col-md-6">
                                <div class="p-3 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-3 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded me-3 text-warning"><i class="bi bi-patch-check-fill fs-4"></i></div>
                                        <h6 class="text-white fw-bold mb-0 text-truncate"><?= htmlspecialchars($cert['title']) ?></h6>
                                    </div>
                                    <p class="extra-small text-white-50 mb-3">Issued on <?= date('M d, Y', strtotime($cert['issued_at'])) ?></p>
                                    <a href="<?= BASE_URL.$cert['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline-warning w-100 rounded-pill">View Certificate</a>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="col-12 text-center py-5 opacity-25">
                                <i class="bi bi-journal-x display-4"></i>
                                <p class="mt-2">No certificates earned yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Attendance History -->
                    <h5 class="fw-bold text-white mb-4 mt-5"><i class="bi bi-calendar-check-fill text-success me-2"></i> Event Attendance</h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover border-white border-opacity-10">
                            <thead class="extra-small text-white-50 text-uppercase">
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php 
                                $history = $pdo->prepare("SELECT e.title, e.event_date, ee.status FROM event_enrollments ee JOIN events e ON ee.event_id = e.id WHERE ee.user_id = ? ORDER BY e.event_date DESC LIMIT 5");
                                $history->execute([$uID]);
                                $historyList = $history->fetchAll();
                                if ($historyList): foreach($historyList as $h): ?>
                                    <tr class="align-middle">
                                        <td class="fw-bold text-white"><?= htmlspecialchars($h['title']) ?></td>
                                        <td><?= date('M d, Y', strtotime($h['event_date'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($h['status'] == 'attended') ? 'success' : (($h['status'] == 'absent') ? 'danger' : 'warning') ?> bg-opacity-10 text-<?= ($h['status'] == 'attended') ? 'success' : (($h['status'] == 'absent') ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($h['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="3" class="text-center py-3 text-white-50">No event history found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings" role="tabpanel">
                    <?php if($msg): ?>
                        <div class="alert alert-<?= $msgType ?> glass-card border-0 mb-4"><?= $msg ?></div>
                    <?php endif; ?>

                    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-white-50">Full Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-transparent border-white border-opacity-10 text-white" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-white-50">New Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control bg-transparent border-white border-opacity-10 text-white" name="password" placeholder="Leave blank to keep current password">
                            </div>
                        </div>
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-white-50">Profile Picture</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control bg-transparent border-white border-opacity-10 text-white" name="avatar">
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-sm-3 col-sm-9">
                                <button type="submit" class="btn btn-premium px-5 shadow-lg">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .nav-tabs .nav-link.active { background: rgba(255,255,255,0.05) !important; border-bottom: 3px solid var(--bs-primary) !important; }
    .nav-tabs .nav-link:hover { background: rgba(255,255,255,0.02); }
</style>

<?php require_once '../includes/footer.php'; ?>


