<?php
require_once '../../includes/header.php';
?>
<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: #1e1b4b url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2); z-index: 0;"></div>
    <div class="container position-relative py-5" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-success px-3 py-2 mb-3">Finance Management</span>
                <h1 class="display-3 fw-bold text-white mb-3">Financial Overview</h1>
                <p class="lead text-white-50 mb-0">Real-time tracking of society revenue, expenses, and budget health.</p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="glass-card p-4 text-center border-0 shadow-lg h-100">
                    <div class="icon-circle bg-success bg-opacity-10 text-success mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-cash-coin fs-3"></i>
                    </div>
                    <h2 class="fw-bold mb-1">$<?= number_format($pdo->query("SELECT SUM(CASE WHEN type='income' THEN amount ELSE -amount END) FROM finance_records WHERE status='approved'")->fetchColumn() ?? 0, 2) ?></h2>
                    <p class="text-muted x-small text-uppercase fw-bold mb-0">Net Revenue</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="glass-card p-4 text-center border-0 shadow-lg h-100">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <h2 class="fw-bold mb-1">$<?= number_format($pdo->query("SELECT SUM(amount) FROM finance_records WHERE status='pending'")->fetchColumn() ?? 0, 2) ?></h2>
                    <p class="text-muted x-small text-uppercase fw-bold mb-0">Record Approvals</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="glass-card p-4 text-center border-0 shadow-lg h-100 border border-primary border-opacity-25" style="background: linear-gradient(145deg, rgba(13, 110, 253, 0.05), rgba(0, 0, 0, 0));">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-wallet2 fs-3"></i>
                    </div>
                    <h2 class="fw-bold mb-1"><?= $pdo->query("SELECT COUNT(*) FROM events WHERE admin_status = 'approved' AND finance_status = 'pending'")->fetchColumn() ?></h2>
                    <p class="text-muted x-small text-uppercase fw-bold mb-2">Event Budgets</p>
                    <a href="approve_events.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">Review Phase 2</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="glass-card p-4 text-center border-0 shadow-lg h-100">
                    <div class="icon-circle bg-info bg-opacity-10 text-info mb-3 mx-auto" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                    <h2 class="fw-bold mb-1">$<?= number_format($pdo->query("SELECT SUM(amount) FROM finance_records WHERE type='income' AND status='approved'")->fetchColumn() ?? 0, 2) ?></h2>
                    <p class="text-muted x-small text-uppercase fw-bold mb-0">Gross Income</p>
                </div>
            </div>
        </div>

        <div class="glass-card border-0 shadow-lg overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">Recent Transactions</h4>
                <button class="btn btn-sm btn-premium px-3">Download Report</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stm = $pdo->query("SELECT * FROM finance_records ORDER BY record_date DESC LIMIT 10");
                while($row = $stm->fetch()):
                    $badge = match($row['status']) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'secondary'
                    };
                    $typeColor = $row['type'] == 'income' ? 'text-success' : 'text-danger';
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td class="text-capitalize"><?= $row['type'] ?></td>
                    <td class="<?= $typeColor ?> fw-bold">$<?= number_format($row['amount'], 2) ?></td>
                    <td><?= date('Y-m-d', strtotime($row['record_date'])) ?></td>
                    <td><span class="badge text-bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
                </tr>
                <?php endwhile; ?>
                <?php if($stm->rowCount() == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted">No transactions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
