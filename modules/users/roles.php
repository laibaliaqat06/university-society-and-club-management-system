<?php 
require_once '../../includes/header.php'; 

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_role'])) {
    $rName = trim($_POST['role_name']);
    $rKey = strtolower(str_replace(' ', '_', $rName)); // Auto-gen key
    $stmt = $pdo->prepare("INSERT INTO sys_roles (role_name, role_key) VALUES (?, ?)");
    try {
        $stmt->execute([$rName, $rKey]);
        echo "<script>window.location.href='roles.php';</script>";
    } catch(Exception $e) { $error = "Role Key exists."; }
}

// Handle Delete (with User Migration Check)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Check if system role
    $check = $pdo->prepare("SELECT role_key, is_system_role FROM sys_roles WHERE id = ?");
    $check->execute([$id]);
    $roleData = $check->fetch();

    if ($roleData['is_system_role'] == 1) {
        $error = "Cannot delete a system protected role.";
    } else {
        // Migrate users to 'suspended'
        $pdo->prepare("UPDATE users SET role = 'suspended', is_active = 0 WHERE role = ?")->execute([$roleData['role_key']]);
        // Delete Access
        $pdo->prepare("DELETE FROM role_access WHERE role_key = ?")->execute([$roleData['role_key']]);
        // Delete Role
        $pdo->prepare("DELETE FROM sys_roles WHERE id = ?")->execute([$id]);
        echo "<script>window.location.href='roles.php';</script>";
    }
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1507537362145-590e38a59301?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3">Security & Access</span>
                <h1 class="display-3 fw-bold text-white mb-3">System Roles</h1>
                <p class="lead text-white-50 mb-0">Define permissions and access levels for all university participants.</p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="card glass-card border-0 shadow-lg mb-5">
            <div class="p-4 border-bottom border-white border-opacity-10">
                <h4 class="mb-0 fw-bold">Role Configuration</h4>
            </div>
    <div class="card-body">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label">New Role Name</label>
                <input type="text" name="role_name" class="form-control" required placeholder="e.g. Librarian">
            </div>
            <div class="col-md-2">
                <button type="submit" name="create_role" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Create</button>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Key</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM sys_roles");
                while($row = $stmt->fetch()):
                    $badge = $row['is_system_role'] ? '<span class="badge text-bg-warning">System</span>' : '<span class="badge text-bg-success">Custom</span>';
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['role_name']) ?></td>
                    <td><code><?= $row['role_key'] ?></code></td>
                    <td><?= $badge ?></td>
                    <td>
                        <?php if(!$row['is_system_role']): ?>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Users with this role will be suspended. Continue?')">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                        <?php else: ?>
                            <span class="text-muted fst-italic">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
