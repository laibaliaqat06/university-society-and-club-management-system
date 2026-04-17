<?php
require_once '../includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([$_GET['delete']]);
    echo "<script>window.location.href='manage.php';</script>";
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-3">Event Logistics</span>
                <h1 class="display-3 fw-bold text-white mb-3">Event Management</h1>
                <p class="lead text-white-50 mb-0">Track, edit, and organize upcoming university experiences.</p>
            </div>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <a href="create.php" class="btn btn-premium btn-lg shadow-lg px-5">
                    <i class="bi bi-plus-lg me-2"></i> Create Event
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="card glass-card border-0 shadow-lg overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Recent Events</h4>
            </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $role = $_SESSION['role'] ?? 'guest';
                $userId = $_SESSION['user_id'] ?? 0;
                $query = "SELECT e.*, 
                          (SELECT COUNT(*) FROM event_enrollments WHERE event_id = e.id AND status = 'pending') as pending_count 
                          FROM events e ";
                $params = [];

                if ($role === 'society_admin') {
                    // Get club id
                    $cStmt = $pdo->prepare("SELECT id FROM clubs WHERE created_by = ?");
                    $cStmt->execute([$userId]);
                    $clubId = $cStmt->fetchColumn();
                    $query .= "WHERE e.club_id = ? ";
                    $params[] = $clubId;
                } elseif ($role === 'event_manager') {
                     $query .= "WHERE e.created_by = ? ";
                     $params[] = $userId;
                }
                
                $query .= "ORDER BY e.event_date DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);

                while($e = $stmt->fetch()):
                ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td>
                        <?= htmlspecialchars($e['title']) ?>
                        <?php if ($e['pending_count'] > 0): ?>
                            <span class="badge bg-danger ms-2" title="Pending Applications"><?= $e['pending_count'] ?> New</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M d, Y', strtotime($e['event_date'])) ?></td>
                    <td><?= htmlspecialchars($e['location']) ?></td>
                    <td>
                        <a href="attendance.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary" title="Manage Enrollments & Attendance">
                            <i class="bi bi-people-fill me-1"></i> Enrollments
                        </a>
                        <a href="gallery_upload.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-warning" title="Manage Photo Gallery">
                            <i class="bi bi-images me-1"></i> Gallery
                        </a>
                        <a href="edit.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-info text-white"><i class="bi bi-pencil"></i></a>
                        <a href="?delete=<?= $e['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
