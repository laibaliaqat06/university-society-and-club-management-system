<?php
require_once '../includes/header.php';

// Check role
$role = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? 0;

// Fetch sponsors query based on role
$query = "SELECT s.*, e.title as event_title 
          FROM event_sponsors s 
          JOIN events e ON s.event_id = e.id ";
$params = [];

if ($role === 'society_admin') {
    // Only see sponsors for events in their society
    $query .= " JOIN clubs c ON e.club_id = c.id WHERE c.created_by = ? ";
    $params[] = $userId;
} elseif ($role === 'event_manager') {
    $query .= " WHERE e.created_by = ? ";
    $params[] = $userId;
}

$query .= " ORDER BY s.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sponsors = $stmt->fetchAll();

?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning px-3 py-2 mb-3">Partnerships</span>
                <h1 class="display-3 fw-bold text-white mb-3">Sponsor Management</h1>
                <p class="lead text-white-50 mb-0">Track and manage event sponsors and their contributions.</p>
            </div>
            <?php if (in_array($role, ['super_admin', 'society_admin', 'event_manager'])): ?>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <a href="sponsor_add.php" class="btn btn-premium btn-lg shadow-lg px-5">
                    <i class="bi bi-plus-lg me-2"></i> Add Sponsor
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="card glass-card border-0 shadow-lg overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-10">
                <h4 class="mb-0 fw-bold">Current Sponsors</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Logo</th>
                                <th>Sponsor Name</th>
                                <th>Event</th>
                                <th>Contribution</th>
                                <?php if (in_array($role, ['super_admin', 'society_admin', 'event_manager'])): ?>
                                <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sponsors)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No sponsors found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($sponsors as $s): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($s['logo_path']) && file_exists('../' . $s['logo_path'])): ?>
                                            <img src="../<?= htmlspecialchars($s['logo_path']) ?>" alt="Logo" class="img-thumbnail rounded" style="max-height: 50px;">
                                        <?php else: ?>
                                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 20px;">
                                                <i class="bi bi-building"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($s['sponsor_name']) ?></td>
                                    <td><a href="view.php?id=<?= $s['event_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($s['event_title']) ?></a></td>
                                    <td class="text-success fw-bold">$<?= number_format($s['contribution_amount'], 2) ?></td>
                                    <?php if (in_array($role, ['super_admin', 'society_admin', 'event_manager'])): ?>
                                    <td>
                                        <a href="sponsor_delete.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to completely remove this sponsor?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
