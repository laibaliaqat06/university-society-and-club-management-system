<?php
require_once '../includes/header.php';
require_once '../core/Clubs.php';

$clubs = new Clubs($pdo);
$clubId = $_GET['id'] ?? 0;
$club = $clubs->getById($clubId);

if (!$club) {
    echo "<div class='alert alert-danger m-4'>Club not found.</div>";
    require_once '../includes/footer.php';
    exit;
}

// Authorization: Super Admin or Club Creator/Admin
$isAdmin = ($_SESSION['role'] === 'super_admin' || $_SESSION['user_id'] == $club['created_by']);
if (!$isAdmin) {
    // Check if user is a club admin
    $userMembership = $pdo->prepare("SELECT role FROM club_memberships WHERE user_id = ? AND club_id = ?");
    $userMembership->execute([$_SESSION['user_id'], $clubId]);
    $role = $userMembership->fetchColumn();
    if ($role !== 'admin' && $role !== 'president') {
        echo "<div class='alert alert-danger m-4'>Unauthorized access.</div>";
        require_once '../includes/footer.php';
        exit;
    }
}

// Handle Role Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_role'])) {
        $userId = $_POST['user_id'];
        $newRole = $_POST['role'];
        $clubs->updateMemberRole($userId, $clubId, $newRole);
        $success = "Role updated successfully!";
    } elseif (isset($_POST['remove_member'])) {
        $userId = $_POST['user_id'];
        $clubs->removeMember($userId, $clubId);
        $success = "Member removed from club.";
    }
}

$members = $clubs->getMembers($clubId);
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary px-3 py-2 mb-3">Community Hub</span>
                <h1 class="display-3 fw-bold text-white mb-3">Membership Roster</h1>
                <p class="lead text-white-50 mb-0">Review your society's active participants and manage internal leadership roles.</p>
            </div>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <a href="view.php?id=<?= $clubId ?>" class="btn btn-premium btn-lg shadow-lg px-5">
                    <i class="bi bi-arrow-left me-2"></i> Society Profile
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-lg bg-dark text-white" style="border-radius: 12px; background: #2b3035 !important;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead class="bg-primary bg-opacity-10">
                            <tr>
                                <th class="p-4 border-0">Member</th>
                                <th class="p-4 border-0">Current Role</th>
                                <th class="p-4 border-0">Joined Date</th>
                                <th class="p-4 border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr class="border-secondary border-opacity-25 align-middle">
                                    <td class="p-4 border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 40px; height: 40px; font-weight: 600;">
                                                <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($member['name']) ?></div>
                                                <div class="text-white-50 small"><?= htmlspecialchars($member['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 border-0">
                                        <span class="badge bg-<?= 
                                            $member['role'] === 'president' ? 'warning' : 
                                            ($member['role'] === 'staff' ? 'info' : 
                                            ($member['role'] === 'coordinator' ? 'success' : 'secondary')) 
                                        ?> text-dark fw-bold px-3 py-2">
                                            <?= ucfirst($member['role']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 border-0 text-white-50">
                                        <?= date('M d, Y', strtotime($member['joined_at'])) ?>
                                    </td>
                                    <td class="p-4 border-0 text-end">
                                        <form method="POST" class="d-inline-flex gap-2">
                                            <input type="hidden" name="user_id" value="<?= $member['id'] ?>">
                                            <select name="role" class="form-select form-select-sm bg-dark text-white border-secondary rounded-pill px-3" style="width: auto; font-size: 0.75rem;">
                                                <option value="member" <?= $member['role'] === 'member' ? 'selected' : '' ?>>Passive Member</option>
                                                <option value="coordinator" <?= $member['role'] === 'coordinator' ? 'selected' : '' ?>>Society Coordinator</option>
                                                <option value="staff" <?= $member['role'] === 'staff' ? 'selected' : '' ?>>Faculty/Staff Advisor</option>
                                                <option value="president" <?= $member['role'] === 'president' ? 'selected' : '' ?>>Society President</option>
                                                <option value="admin" <?= $member['role'] === 'admin' ? 'selected' : '' ?>>System Admin</option>
                                            </select>
                                            <button type="submit" name="update_role" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size: 0.75rem;">Apply</button>
                                            <button type="submit" name="remove_member" class="btn btn-sm btn-outline-danger rounded-circle p-1" onclick="return confirm('Are you sure you want to remove this member?')" style="width: 30px; height: 30px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
