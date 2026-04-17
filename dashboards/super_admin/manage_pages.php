<?php 
require_once '../../includes/header.php'; 

// Handle Create Page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pName = $_POST['page_name'];
    $pUrl = $_POST['page_url'];
    $pParent = $_POST['parent_id'];
    $pIcon = $_POST['icon_class'];
    $roles = $_POST['roles'] ?? []; // Array of role_keys

    $pdo->beginTransaction();
    try {
        // 1. Insert Page
        $stmt = $pdo->prepare("INSERT INTO sys_pages (parent_id, page_name, page_url, icon_class) VALUES (?,?,?,?)");
        $stmt->execute([$pParent, $pName, $pUrl, $pIcon]);
        $pageId = $pdo->lastInsertId();

        // 2. Assign Permissions
        $permStmt = $pdo->prepare("INSERT INTO role_access (role_key, page_id) VALUES (?, ?)");
        foreach($roles as $rKey) {
            $permStmt->execute([$rKey, $pageId]);
        }
        $pdo->commit();
    } catch(Exception $e) {
        $pdo->rollBack();
        die($e->getMessage());
    }
}
?>

<style>
    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }
    .role-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .role-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--bs-primary);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .role-card.selected {
        border-color: var(--bs-primary);
        background: rgba(13, 110, 253, 0.1);
    }
    .role-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .role-icon {
        width: 40px;
        height: 40px;
        background: var(--bs-primary);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.2rem;
    }
    .role-name {
        font-weight: 600;
        font-size: 1.1rem;
        color: #fff;
    }
    .role-desc {
        font-size: 0.85rem;
        color: #adb5bd;
        margin-bottom: 0;
        line-height: 1.4;
    }
    .authority-tag {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--bs-primary);
        margin-bottom: 0.25rem;
        display: block;
        font-weight: 700;
    }
    .form-check-input-custom {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 20px;
        height: 20px;
    }
    .premium-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 2rem;
        border-radius: 15px 15px 0 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
</style>

<div class="container-fluid pb-5">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="premium-header">
                    <h3 class="mb-1 text-white">Create System Page</h3>
                    <p class="text-muted mb-0">Configure new menu items and define their access level across roles.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row g-4 mb-5">
                            <div class="col-md-3">
                                <label class="form-label text-light fw-bold">Page Identity</label>
                                <p class="small text-muted">Unique name shown in the sidebar menu.</p>
                                <input type="text" name="page_name" class="form-control form-control-lg bg-dark text-white border-secondary" placeholder="e.g. Analytics Report" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-light fw-bold">Relative URL</label>
                                <p class="small text-muted">Path relative to the root directory.</p>
                                <input type="text" name="page_url" class="form-control form-control-lg bg-dark text-white border-secondary" placeholder="reports/analytics.php" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-light fw-bold">Parent Navigation</label>
                                <p class="small text-muted">Nest this page under a specific menu.</p>
                                <select name="parent_id" class="form-select form-select-lg bg-dark text-white border-secondary">
                                    <option value="0">Root (Main Menu)</option>
                                    <?php 
                                    $parents = $pdo->query("SELECT * FROM sys_pages WHERE page_url = '#'")->fetchAll();
                                    foreach($parents as $p) echo "<option value='{$p['id']}'>{$p['page_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-light fw-bold">Visual Icon</label>
                                <p class="small text-muted">Bootstrap or FontAwesome class.</p>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark border-secondary text-primary"><i class="bi bi-grid"></i></span>
                                    <input type="text" name="icon_class" class="form-control bg-dark text-white border-secondary" placeholder="bi bi-circle">
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary opacity-25 my-5">

                        <div class="mb-4">
                            <h4 class="text-white mb-1"><i class="bi bi-shield-check text-primary me-2"></i> Role & Authority Selection</h4>
                            <p class="text-muted">Select which user roles have the authority to access this system page.</p>
                        </div>

                        <div class="role-grid">
                            <?php 
                            $roles = $pdo->query("SELECT * FROM sys_roles")->fetchAll();
                            $authorities = [
                                'super_admin' => ['desc' => 'Full Administrative Access', 'auth' => 'ROOT AUTHORITY', 'icon' => 'bi-shield-fill-check'],
                                'administrator' => ['desc' => 'High-level System Oversight', 'auth' => 'FULL ADMIN', 'icon' => 'bi-person-badge'],
                                'student' => ['desc' => 'Campus Life & Society Access', 'auth' => 'STANDARD USER', 'icon' => 'bi-mortarboard'],
                                'suspended' => ['desc' => 'Restricted System Functions', 'auth' => 'LIMITED', 'icon' => 'bi-exclamation-octagon'],
                                'society_admin' => ['desc' => 'Manage Society & Memberships', 'auth' => 'SOCIETY LEAD', 'icon' => 'bi-building'],
                                'event_manager' => ['desc' => 'Coordinate Events & Schedule', 'auth' => 'OPS LEAD', 'icon' => 'bi-calendar3'],
                                'finance_manager' => ['desc' => 'Financial Records & Budgets', 'auth' => 'TREASURY', 'icon' => 'bi-cash-stack'],
                                'member' => ['desc' => 'Participate in Activities', 'auth' => 'MEMBER', 'icon' => 'bi-person-check'],
                                'guest' => ['desc' => 'View Public Information Only', 'auth' => 'READ ONLY', 'icon' => 'bi-eye'],
                            ];

                            foreach($roles as $r): 
                                $meta = $authorities[$r['role_key']] ?? ['desc' => 'Standard System Role', 'auth' => 'ACCESS ONLY', 'icon' => 'bi-circle'];
                            ?>
                            <div class="role-card" onclick="this.querySelector('input').click(); this.classList.toggle('selected')">
                                <input class="form-check-input form-check-input-custom" type="checkbox" name="roles[]" value="<?= $r['role_key'] ?>" checked onclick="event.stopPropagation()">
                                <span class="authority-tag"><?= $meta['auth'] ?></span>
                                <div class="role-header">
                                    <div class="role-icon"><i class="bi <?= $meta['icon'] ?>"></i></div>
                                    <div class="role-name"><?= $r['role_name'] ?></div>
                                </div>
                                <p class="role-desc"><?= $meta['desc'] ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-5 text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow-sm rounded-pill">
                                <i class="bi bi-plus-circle me-2"></i> Register System Page
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize selected state for cards
    document.querySelectorAll('.role-card input:checked').forEach(input => {
        input.closest('.role-card').classList.add('selected');
    });
</script>

<?php require_once '../../includes/footer.php'; ?>