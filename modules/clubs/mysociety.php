<?php
require_once '../../includes/header.php';
require_once '../../includes/Clubs.php';

// Ensure user is authorized (Society Head or Super Admin)
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='" . BASE_URL . "login.php';</script>";
    exit;
}

$clubs = new Clubs($pdo);
// Find the club created by this user
$stmt = $pdo->prepare("SELECT * FROM clubs WHERE created_by = ?");
$stmt->execute([$_SESSION['user_id']]);
$club = $stmt->fetch();
?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1523580494863-6f30312248f5?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3">Society Administration</span>
                <h1 class="display-3 fw-bold text-white mb-3">Society Console</h1>
                <p class="lead text-white-50 mb-0">Manage your society's identity, members, and internal operations.</p>
            </div>
            <div class="col-lg-4 text-lg-end pt-4 pt-lg-0">
                <a href="<?= BASE_URL ?>modules/clubs/view.php?id=<?= $club['id'] ?? 0 ?>" class="btn btn-premium btn-lg shadow-lg px-5">
                    <i class="bi bi-eye me-2"></i> Public View
                </a>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
<?php if (!$club): ?>
        <div class="alert alert-warning">
            <h5><i class="icon bi bi-exclamation-triangle"></i> No Society Found!</h5>
            You have not registered a society yet.
            <div class="mt-3">
                <a href="create.php" class="btn btn-primary">Register New Society</a>
            </div>
        </div>
    <?php else: 
        $members = $clubs->getMembers($club['id']);
    ?>
        <div class="row">
            <!-- Club Overview -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center mb-3">
                            <?php if($club['logo']): 
                                $sLogo = $club['logo'];
                                if (strpos($sLogo, 'http') !== 0) {
                                    $sLogo = BASE_URL . $sLogo;
                                }
                            ?>
                                <img class="profile-user-img img-fluid img-circle" src="<?= $sLogo ?>" alt="Logo" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-people-fill display-1 text-secondary"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="profile-username text-center"><?= htmlspecialchars($club['name']) ?></h3>
                        <p class="text-muted text-center"><?= htmlspecialchars($club['description']) ?></p>
                        
                        <div class="d-grid gap-2">
                             <a href="edit.php?id=<?= $club['id'] ?>" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Members List -->
            <div class="col-md-8">
                 <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Approved Members (<?= count($members) ?>)</h3>
                    </div>
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Joined Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($members) > 0): ?>
                                    <?php foreach($members as $m): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($m['name']) ?></td>
                                        <td><?= htmlspecialchars($m['email']) ?></td>
                                        <td><?= date('M d, Y', strtotime($m['joined_at'])) ?></td>
                                        <td><span class="badge bg-success">Active</span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">No members found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

