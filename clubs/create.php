<?php
require_once '../includes/header.php';

// Fetch users for dropdowns
$usersList = $pdo->query("SELECT id, name, email FROM users ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    
    // File Upload Helper
    function uploadFile($file, $destDir) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            if (!is_dir($destDir)) mkdir($destDir, 0777, true);
            move_uploaded_file($file['tmp_name'], $destDir . $filename);
            return BASE_URL . 'uploads/' . $filename;
        }
        return null;
    }

    $logo = uploadFile($_FILES['logo'], '../uploads/');
    $cover = uploadFile($_FILES['cover_image'], '../uploads/');

    $president_info = $_POST['president_info'] ?? '';
    // Handle selections (can be array or string depending on form modification)
    $fac_adv = $_POST['faculty_advisors'] ?? '';
    $faculty_advisors = is_array($fac_adv) ? implode(',', $fac_adv) : $fac_adv;
    
    $core_com = $_POST['core_committee'] ?? '';
    $core_committee = is_array($core_com) ? implode(',', $core_com) : $core_com;
    $joining_rules = $_POST['joining_rules'] ?? '';
    $exit_rules = $_POST['exit_rules'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO clubs (name, description, logo, cover_image, contact_email, contact_phone, president_info, faculty_advisors, core_committee, joining_rules, exit_rules, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $logo, $cover, $contact_email, $contact_phone, $president_info, $faculty_advisors, $core_committee, $joining_rules, $exit_rules, $_SESSION['user_id']]);
        echo "<script>alert('Club created successfully!'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-3">New Initiative</span>
                <h1 class="display-3 fw-bold text-white mb-3">Start a Society</h1>
                <p class="lead text-white-50 mb-0">Empower your vision and build a community for your passion.</p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="card card-primary card-outline mb-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Club Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo (Square)</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Cover Image (Banner)</label>
                            <input type="file" class="form-control" name="cover_image" accept="image/*">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5"></textarea>
                        </div>
                        
                        <div class="col-12 mt-4 mb-2">
                            <h5 class="fw-bold border-bottom pb-2">Leadership & Committee</h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">President</label>
                            <select name="president_info" class="form-select">
                                <option value="">Select President</option>
                                <?php foreach($usersList as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Faculty Advisor</label>
                            <select name="faculty_advisors" class="form-select">
                                <option value="">Select Faculty Advisor</option>
                                <?php foreach($usersList as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Core Committee Lead</label>
                            <select name="core_committee" class="form-select">
                                <option value="">Select Core Committee Lead</option>
                                <?php foreach($usersList as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 mt-4 mb-2">
                            <h5 class="fw-bold border-bottom pb-2">Membership Guidelines</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rules for Joining</label>
                            <textarea class="form-control" name="joining_rules" rows="4" placeholder="Eligibility, process, fees..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rules for Exiting</label>
                            <textarea class="form-control" name="exit_rules" rows="4" placeholder="Resignation process, term limits..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Create Club</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
