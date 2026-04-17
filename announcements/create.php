<?php
require_once '../includes/header.php';
require_once '../core/Announcements.php';
require_once '../core/Notifications.php';

$role = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? 0;

if (!in_array($role, ['super_admin', 'society_admin'])) {
    die('<div class="alert alert-danger m-5">Access Denied</div>');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = $_POST['type'] ?? 'info';
    $isPinned = isset($_POST['is_pinned']) ? 1 : 0;
    $validUntil = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null;
    
    $annObj = new Announcements($pdo);
    $notifObj = new Notifications($pdo);
    
    if (empty($title) || empty($message)) {
        $error = 'Title and Message are required.';
    } else {
        if ($role === 'super_admin') {
            if ($annObj->createGlobal($title, $message, $userId, $type, $isPinned, $validUntil)) {
                $annId = $pdo->lastInsertId();
                $notifObj->dispatchToAll($annId);
                $success = 'Global announcement posted successfully!';
            } else {
                $error = 'Failed to create global announcement.';
            }
        } elseif ($role === 'society_admin') {
            $cStmt = $pdo->prepare("SELECT id FROM clubs WHERE created_by = ? LIMIT 1");
            $cStmt->execute([$userId]);
            $clubId = $cStmt->fetchColumn();
            
            if ($clubId) {
                if ($annObj->createForSociety($title, $message, $clubId, $userId, $type, $isPinned, $validUntil)) {
                    $annId = $pdo->lastInsertId();
                    $notifObj->dispatchToSociety($annId, $clubId);
                    $success = 'Society announcement posted successfully!';
                } else {
                    $error = 'Failed to create society announcement.';
                }
            } else {
                $error = 'You are not assigned as head of any society.';
            }
        }
    }
}
?>

<div class="app-content-header">
    <div class="container-fluid">
        <h3 class="fw-bold mb-0">Post New Announcement</h3>
    </div>
</div>

<div class="app-content mt-3">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card glass-card border-0 shadow-lg mb-5">
                    <div class="p-4 border-bottom border-white border-opacity-10 bg-primary bg-opacity-10 text-primary">
                        <h4 class="mb-0 fw-bold"><i class="bi bi-megaphone me-2"></i> Announcement Details</h4>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <?php if($success): ?>
                            <div class="alert alert-success shadow-sm rounded-3"><i class="bi bi-check-circle-fill me-2"></i> <?= $success ?></div>
                        <?php endif; ?>
                        <?php if($error): ?>
                            <div class="alert alert-danger shadow-sm rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="type" id="type" class="form-select">
                                            <option value="info" selected>ℹ️ Information / Update</option>
                                            <option value="urgent">🚨 Urgent / Important</option>
                                            <option value="success">🎉 Achievement / Success</option>
                                            <option value="event">📅 Event Related</option>
                                        </select>
                                        <label for="type">Announcement Category</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="datetime-local" name="valid_until" id="valid_until" class="form-control">
                                        <label for="valid_until">Expiry Date (Optional)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" name="title" id="title" class="form-control" placeholder="Enter headline" required>
                                <label for="title">Headline / Subject</label>
                            </div>
                            
                            <div class="form-floating mb-4">
                                <textarea name="message" id="message" class="form-control" style="height: 180px" placeholder="Write the details here" required></textarea>
                                <label for="message">Full Message Content</label>
                            </div>

                            <div class="form-check form-switch mb-4 p-0 ps-5 d-flex align-items-center">
                                <input class="form-check-input me-3" type="checkbox" name="is_pinned" id="is_pinned" style="width: 2.5em; height: 1.25em;">
                                <label class="form-check-label fw-bold text-white-50" for="is_pinned">
                                    Pin this announcement to the top 📍
                                </label>
                            </div>
                            
                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-premium btn-lg shadow rounded-pill py-3">
                                    <i class="bi bi-send-fill me-2"></i> Publish Announcement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
