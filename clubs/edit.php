<?php
require_once '../includes/header.php';
require_once '../core/Clubs.php';

$clubs = new Clubs($pdo);
$id = $_GET['id'] ?? 0;
$club = $clubs->getById($id);

// Fetch users for dropdowns
$usersList = $pdo->query("SELECT id, name, email FROM users ORDER BY name")->fetchAll();

if (!$club) {
    echo "<div class='alert alert-danger m-4'>Club not found.</div>";
    require_once '../includes/footer.php';
    exit;
}

// Authorization Check (Super Admin or Creator/Society Head)
if ($_SESSION['role'] !== 'super_admin' && $_SESSION['user_id'] != $club['created_by']) {
    echo "<div class='alert alert-danger m-4'>Unauthorized access.</div>";
    require_once '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_gallery'])) {
    $imgId = (int)$_POST['delete_gallery'];
    $pdo->prepare("DELETE FROM club_gallery WHERE id = ? AND club_id = ?")->execute([$imgId, $id]);
    echo "<script>alert('Image deleted!'); window.location.href='edit.php?id=$id';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_gallery'])) {
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

    $president_info = $_POST['president_info'] ?? '';
    // Handle selections (can be array or string depending on form modification)
    $fac_adv = $_POST['faculty_advisors'] ?? '';
    $faculty_advisors = is_array($fac_adv) ? implode(',', $fac_adv) : $fac_adv;
    
    $core_com = $_POST['core_committee'] ?? '';
    $core_committee = is_array($core_com) ? implode(',', $core_com) : $core_com;
    $joining_rules = $_POST['joining_rules'] ?? '';
    $exit_rules = $_POST['exit_rules'] ?? '';

    // Keep old images if no new upload
    $logo = !empty($_FILES['logo']['name']) ? uploadFile($_FILES['logo'], '../uploads/') : $club['logo'];
    $cover = !empty($_FILES['cover_image']['name']) ? uploadFile($_FILES['cover_image'], '../uploads/') : $club['cover_image'];

    try {
        $stmt = $pdo->prepare("UPDATE clubs SET name=?, description=?, logo=?, cover_image=?, contact_email=?, contact_phone=?, president_info=?, faculty_advisors=?, core_committee=?, joining_rules=?, exit_rules=? WHERE id=?");
        $stmt->execute([$name, $description, $logo, $cover, $contact_email, $contact_phone, $president_info, $faculty_advisors, $core_committee, $joining_rules, $exit_rules, $id]);
        
        // Handle gallery images
        if (!empty($_FILES['gallery_images']['name'][0])) {
            foreach ($_FILES['gallery_images']['name'] as $key => $filename) {
                if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $newFilename = uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], '../uploads/' . $newFilename);
                    $url = 'uploads/' . $newFilename;
                    $pdo->prepare("INSERT INTO club_gallery (club_id, image_url) VALUES (?, ?)")->execute([$id, $url]);
                }
            }
        }

        echo "<script>alert('Club updated successfully!'); window.location.href='view.php?id=$id';</script>";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3">Identity Update</span>
                <h1 class="display-3 fw-bold text-white mb-3">Edit Society</h1>
                <p class="lead text-white-50 mb-0">Refine your society's presence and keep your community informed.</p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="card card-warning card-outline mb-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Club Name</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($club['name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="<?= htmlspecialchars($club['contact_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" value="<?= htmlspecialchars($club['contact_phone'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Logo</label><br>
                            <?php if($club['logo']): ?>
                                <img src="<?= $club['logo'] ?>" style="width: 50px; height: 50px; object-fit: cover;" class="mb-2 rounded">
                            <?php endif; ?>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            <small class="text-muted">Leave empty to keep current logo</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Current Banner</label><br>
                            <?php if($club['cover_image']): ?>
                                <img src="<?= $club['cover_image'] ?>" style="height: 100px; object-fit: cover; width: 100%;" class="mb-2 rounded">
                            <?php endif; ?>
                            <input type="file" class="form-control" name="cover_image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current banner</small>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Description (About Us)</label>
                            <textarea class="form-control" name="description" rows="5"><?= htmlspecialchars($club['description']) ?></textarea>
                        </div>

                        <div class="col-12 mt-4 mb-2">
                            <h5 class="fw-bold border-bottom pb-2">Society Gallery</h5>
                        </div>

                        <?php 
                        $gallery = $pdo->query("SELECT * FROM club_gallery WHERE club_id = $id")->fetchAll();
                        if($gallery): 
                        ?>
                        <div class="col-12 mb-3">
                            <label class="form-label d-block text-white-50 small">Existing Public Images</label>
                            <div class="row g-2">
                                <?php foreach($gallery as $img): 
                                    $imgSrc = $img['image_url'];
                                    if (strpos($imgSrc, 'http') !== 0) {
                                        $imgSrc = BASE_URL . $imgSrc;
                                    }
                                ?>
                                <div class="col-4 col-md-2 position-relative">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" class="w-100 rounded shadow-sm border border-secondary" style="height: 100px; object-fit: cover;">
                                    <form method="POST" class="position-absolute top-0 end-0 m-1">
                                        <input type="hidden" name="delete_gallery" value="<?= $img['id'] ?>">
                                        <button class="btn btn-danger btn-sm p-1 shadow" onclick="return confirm('Delete this image?')" style="width: 28px; height: 28px; line-height: 1;"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-12 mb-3 mt-2">
                            <label class="form-label">Upload New Gallery Images</label>
                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                            <small class="text-muted">You can select multiple images to add to the public gallery.</small>
                        </div>
                        
                        <div class="col-12 mt-4 mb-2">
                            <h5 class="fw-bold border-bottom pb-2">Society Management & Roles</h5>
                            <p class="text-muted small">Roles like <strong>President, Coordinator, and Staff</strong> are now managed through the Membership Roster. This ensures they are properly linked to system accounts.</p>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="p-4 rounded border border-primary border-opacity-25 bg-primary bg-opacity-10 d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i> Manage Society Board</h6>
                                    <p class="mb-0 small text-white-50">Assign roles, upgrade members to officials, or remove participants.</p>
                                </div>
                                <a href="members.php?id=<?= $id ?>" class="btn btn-primary px-4 rounded-pill">
                                    <i class="bi bi-person-gear me-2"></i> Membership Roster
                                </a>
                            </div>
                        </div>

                        <div class="col-12 mt-4 mb-2">
                            <h5 class="fw-bold border-bottom pb-2">Membership Guidelines</h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rules for Joining</label>
                            <textarea class="form-control" name="joining_rules" rows="4" placeholder="Eligibility, process, fees..."><?= htmlspecialchars($club['joining_rules'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rules for Exiting</label>
                            <textarea class="form-control" name="exit_rules" rows="4" placeholder="Resignation process, term limits..."><?= htmlspecialchars($club['exit_rules'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Update Club</button>
                    <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
