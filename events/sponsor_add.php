<?php
require_once '../includes/header.php';

$role = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? 0;

if (!in_array($role, ['super_admin', 'society_admin', 'event_manager'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Access Denied</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// Fetch events the user can add sponsors to
$query = "SELECT id, title FROM events ";
$params = [];

if ($role === 'society_admin') {
    $query .= " WHERE club_id IN (SELECT id FROM clubs WHERE created_by = ?) ";
    $params[] = $userId;
} elseif ($role === 'event_manager') {
    $query .= " WHERE created_by = ? ";
    $params[] = $userId;
}
$query .= " ORDER BY event_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? '';
    $sponsor_name = trim($_POST['sponsor_name'] ?? '');
    $contribution = floatval($_POST['contribution'] ?? 0);
    $logo_path = '';

    if (empty($event_id) || empty($sponsor_name)) {
        $error = "Event and Sponsor Name are required.";
    } else {
        // Handle File Upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/sponsors/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]+/", "", basename($_FILES['logo']['name']));
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                $logo_path = 'uploads/sponsors/' . $filename;
            } else {
                $error = "Failed to upload logo.";
            }
        }

        if (!isset($error)) {
            $stmt = $pdo->prepare("INSERT INTO event_sponsors (event_id, sponsor_name, contribution_amount, logo_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$event_id, $sponsor_name, $contribution, $logo_path]);
            echo "<script>window.location.href='sponsors.php';</script>";
            exit;
        }
    }
}
?>

<div class="app-content mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card glass-card border-0 shadow-lg">
                    <div class="card-header border-bottom border-white border-opacity-10 py-3">
                        <h4 class="mb-0 fw-bold"><i class="bi bi-briefcase me-2"></i>Add New Sponsor</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger bg-danger text-white border-0"><i class="bi bi-exclamation-triangle me-2"></i><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Event <span class="text-danger">*</span></label>
                                <select name="event_id" class="form-select border-opacity-25 shadow-sm bg-dark text-white border-secondary" required>
                                    <option value="">-- Choose Event --</option>
                                    <?php foreach($events as $e): ?>
                                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Sponsor Name <span class="text-danger">*</span></label>
                                <input type="text" name="sponsor_name" class="form-control border-opacity-25 shadow-sm" placeholder="e.g. XYZ Software House" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Contribution Amount ($)</label>
                                <input type="number" step="0.01" name="contribution" class="form-control border-opacity-25 shadow-sm" placeholder="e.g. 2000.00">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Sponsor Logo (Optional)</label>
                                <input type="file" name="logo" class="form-control border-opacity-25 shadow-sm" accept="image/*">
                                <div class="form-text mt-2 text-muted">Upload a high-quality logo image (PNG or JPG).</div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-premium btn-lg shadow">
                                    <i class="bi bi-save me-2"></i> Save Sponsor
                                </button>
                                <a href="sponsors.php" class="btn btn-light mt-3">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
