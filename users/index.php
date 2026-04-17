<?php 
require_once '../includes/header.php'; 

// Fetch Roles for Dropdown
$roles = $pdo->query("SELECT * FROM sys_roles")->fetchAll();

// Fetch Clubs for Dropdown
$clubs = $pdo->query("SELECT id, name FROM clubs ORDER BY name")->fetchAll();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'assign_club') {
        $user_id = $_POST['user_id'];
        $club_id = $_POST['club_id'];
        $club_role = $_POST['club_role'];
        
        // Check if already assigned
        $check = $pdo->prepare("SELECT id FROM club_memberships WHERE user_id = ? AND club_id = ?");
        $check->execute([$user_id, $club_id]);
        
        if($check->fetch()) {
            $stmt = $pdo->prepare("UPDATE club_memberships SET role = ? WHERE user_id = ? AND club_id = ?");
            $stmt->execute([$club_role, $user_id, $club_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO club_memberships (user_id, club_id, role, status) VALUES (?, ?, ?, 'approved')");
            $stmt->execute([$user_id, $club_id, $club_role]);
        }
    } else {
        // Handle Add User
        $name = $_POST['name'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = $_POST['password'] ?? '123456'; 
        $pass = password_hash($password, PASSWORD_DEFAULT); 
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $role, $pass]);
        } catch(Exception $e) { $error = "Email already exists."; }
    }
}
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-3">Administration</span>
                <h1 class="display-3 fw-bold text-white mb-3">User Management</h1>
                <p class="lead text-white-50 mb-0">Manage system access, assign roles, and audit user activity.</p>
            </div>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <button class="btn btn-premium btn-lg shadow-lg px-5" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill me-2"></i> Add New User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="card glass-card border-0 shadow-lg overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-10">
                <h4 class="mb-0 fw-bold">Active System Users</h4>
            </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead class="text-white-50 small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th class="py-3">User Details</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php
                    $i = 1;
                    $users = $pdo->query("SELECT u.*, r.role_name FROM users u JOIN sys_roles r ON u.role = r.role_key");
                    while($u = $users->fetch()):
                        $roleColor = match($u['role']) {
                            'super_admin' => 'danger',
                            'society_admin' => 'warning',
                            'member' => 'success',
                            default => 'secondary'
                        };
                    ?>
                    <tr class="align-middle border-bottom border-secondary border-opacity-25">
                        <td class="ps-4 py-3 text-white-50"><?= $i++ ?></td>
                        <td class="py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 40px; height: 40px; font-weight: 500;">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div class="lh-sm">
                                    <div class="fw-bold"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="text-white-50 x-small"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge rounded-pill bg-<?= $roleColor ?> bg-opacity-10 text-<?= $roleColor ?> border border-<?= $roleColor ?> border-opacity-25 px-3 py-2">
                                <?= htmlspecialchars($u['role_name']) ?>
                            </span>
                        </td>
                        <td class="py-3">
                            <?= $u['is_active'] ? 
                                '<span class="status-pill status-active"></span> Active' : 
                                '<span class="status-pill status-inactive"></span> Suspended' ?>
                        </td>
                        <td class="py-3 text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-info border-0" data-bs-toggle="modal" data-bs-target="#assignClubModal" onclick="document.getElementById('assign_user_id').value = <?= $u['id'] ?>;" title="Assign to Club">
                                <i class="bi bi-building"></i>
                            </button>
                            <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-light border-0"><i class="bi bi-pencil-square"></i></a>
                            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Delete this user?')"><i class="bi bi-person-x"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
    .status-pill {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 5px;
    }
    .status-active { background-color: #28a745; box-shadow: 0 0 5px #28a745; }
    .status-inactive { background-color: #dc3545; box-shadow: 0 0 5px #dc3545; }
</style>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select" required>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['role_key'] ?>"><?= $r['role_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<div class="modal fade" id="assignClubModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content text-dark">
            <input type="hidden" name="action" value="assign_club">
            <input type="hidden" name="user_id" id="assign_user_id">
            <div class="modal-header">
                <h5 class="modal-title">Assign User to Club</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Club</label>
                    <select name="club_id" class="form-select" required>
                        <option value="">Select a Club</option>
                        <?php foreach($clubs as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Role in Club</label>
                    <select name="club_role" class="form-select" required>
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                        <option value="president">President</option>
                        <option value="coordinator">Coordinator</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Assign Role</button>
            </div>
        </form>
    </div>
</div>
