<?php 
require_once '../../../includes/header.php'; 

// Check if user is super_admin (security check)
if ($_SESSION['user_role'] !== 'super_admin') {
    echo "<div class='alert alert-danger'>Access Denied.</div>";
    require_once '../../../includes/footer.php';
    exit;
}

// Fetch logs
$query = "SELECT l.*, u.name as user_name, u.email as user_email 
          FROM audit_logs l 
          LEFT JOIN users u ON l.user_id = u.id 
          ORDER BY l.created_at DESC";
$logs = $pdo->query($query);
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">System Audit Logs</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($log = $logs->fetch()): ?>
                    <tr>
                        <td>
                            <small class="text-muted d-block"><?= date('Y-m-d', strtotime($log['created_at'])) ?></small>
                            <?= date('H:i:s', strtotime($log['created_at'])) ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($log['user_name'] ?? 'System') ?></strong>
                            <?php if ($log['user_email']): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($log['user_email']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($log['action']) ?></span>
                        </td>
                        <td>
                            <?php if ($log['target_table']): ?>
                                <span class="badge bg-secondary"><?= htmlspecialchars($log['target_table']) ?></span>
                                <?php if ($log['target_id']): ?>
                                    <small class="text-muted">ID: <?= $log['target_id'] ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?= htmlspecialchars($log['details']) ?></small>
                        </td>
                        <td>
                            <small class="text-muted"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></small>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($logs->rowCount() === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No logs found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../../includes/footer.php'; ?>
