<?php 
require_once 'core/session.php'; // Ensures login
require_once 'includes/header.php'; 

$uID = $_SESSION['user_id'];
$msg = "";
$msgType = "";

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    
    // 1. Handle File Upload
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = "uploads/";
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
    $msg = "Profile updated successfully!";
    $msgType = "success";
}

// Fetch Latest User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uID]);
$user = $stmt->fetch();
?>

<div class="row g-4 hero-glow">
    <div class="col-md-4">
        <div class="card glass-card mb-4 text-center p-4">
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
            <p class="text-white-50"><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></p>
            <hr class="border-white border-opacity-10">
            <div class="text-start">
                <p class="small text-white-50 mb-1">Email Address</p>
                <p class="text-white mb-3"><?= htmlspecialchars($user['email']) ?></p>
                <p class="small text-white-50 mb-1">Registration No</p>
                <p class="text-white mb-0"><?= htmlspecialchars($user['registration_no'] ?? $user['identity_no']) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card glass-card">
            <div class="card-header border-bottom border-white border-opacity-10">
                <h3 class="card-title">Account Settings</h3>
            </div>
            <div class="card-body p-4">
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
                            <button type="submit" class="btn btn-premium px-5">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>