<?php
require_once '../core/session.php';
require_once '../core/Events.php';

$eventId = $_GET['id'] ?? 0;
$eventsObj = new Events($pdo);
$event = $eventsObj->getById($eventId);

if (!$event) {
    die("Event not found.");
}

// Security Check: Only Admin or Creator (Society Head) can upload
$isAuthorized = ($_SESSION['role'] === 'super_admin' || $_SESSION['user_id'] == $event['created_by']);
if (!$isAuthorized) {
    die("Unauthorized access.");
}

$message = "";

// Handle Upload
if (isset($_POST['upload'])) {
    $uploadDir = '../uploads/events/' . $eventId . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $totalFiles = count($_FILES['photos']['name']);
    $successCount = 0;

    for ($i = 0; $i < $totalFiles; $i++) {
        $fileName = time() . '_' . basename($_FILES['photos']['name'][$i]);
        $targetFile = $uploadDir . $fileName;
        $dbPath = 'uploads/events/' . $eventId . '/' . $fileName;

        if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $targetFile)) {
            if ($eventsObj->addGalleryImage($eventId, $dbPath)) {
                $successCount++;
            }
        }
    }
    $message = "Successfully uploaded $successCount photos.";
}

// Handle Delete
if (isset($_GET['delete_img'])) {
    if ($eventsObj->deleteGalleryImage($_GET['delete_img'])) {
        header("Location: gallery_upload.php?id=$eventId&msg=Image Deleted");
        exit;
    }
}

$gallery = $eventsObj->getGallery($eventId);

require_once '../includes/header.php';
?>

<div class="app-content-header mb-5 py-5 bg-dark text-white rounded-bottom-4">
    <div class="container py-4">
        <h1 class="display-4 fw-bold">Manage Event Gallery</h1>
        <p class="lead">Event: <?= htmlspecialchars($event['title']) ?></p>
        <a href="manage.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Back to Management</a>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card glass-card p-4 shadow-sm border-0 mb-4">
                <h4 class="fw-bold mb-4">Upload Memories</h4>
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Select Photos</label>
                        <input type="file" name="photos[]" class="form-control" multiple accept="image/*" required>
                        <div class="form-text">You can select multiple images at once.</div>
                    </div>
                    <button type="submit" name="upload" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="bi bi-cloud-upload me-2"></i> Start Upload
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card glass-card p-4 shadow-sm border-0">
                <h4 class="fw-bold mb-4">Current Gallery</h4>
                <?php if (empty($gallery)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-image-fill display-1 opacity-25 d-block mb-3"></i>
                        <p>No photos uploaded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($gallery as $img): ?>
                            <div class="col-md-4">
                                <div class="position-relative gallery-item-manage">
                                    <img src="<?= BASE_URL . $img['image'] ?>" class="img-fluid rounded shadow-sm" style="height: 150px; width: 100%; object-fit: cover;">
                                    <a href="?id=<?= $eventId ?>&delete_img=<?= $img['id'] ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 shadow" onclick="return confirm('Remove this photo?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.gallery-item-manage:hover img {
    filter: brightness(0.7);
    transition: 0.3s;
}
</style>

<?php require_once '../includes/footer.php'; ?>
