
<div class="card glass-card mb-4 overflow-hidden hero-glow" style="height: 250px; border:none;">
    <div class="position-absolute w-100 h-100" style="background: url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.4) saturate(1.2);"></div>
    <div class="card-body position-relative d-flex align-items-end p-5 h-100">
        <div>
            <span class="badge bg-success px-3 py-2 mb-2">Financial Oversight</span>
            <h2 class="display-6 fw-bold text-white mb-0">Treasury Console</h2>
            <p class="text-white-50 lead mb-0">Maintaining fiscal transparency and growth.</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Budget -->
    <div class="col-lg-4 col-12">
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>$<?= number_format($pdo->query("SELECT SUM(CASE WHEN type='income' THEN amount ELSE -amount END) FROM finance_records WHERE status='approved'")->fetchColumn() ?? 0, 2) ?></h3>
                <p>Track Society Funds</p>
            </div>
            <div class="icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <a href="<?= BASE_URL ?>finance/index.php" class="small-box-footer">View Funds <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>

    <!-- Event Budget Approvals -->
    <div class="col-lg-4 col-12">
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3><?= $pdo->query("SELECT COUNT(*) FROM events WHERE admin_status = 'approved' AND finance_status = 'pending'")->fetchColumn() ?></h3>
                <p>Event Budget Apps</p>
            </div>
            <div class="icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <a href="<?= BASE_URL ?>finance/approve_events.php" class="small-box-footer">Review Budgets <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>

    <!-- Reports -->
    <div class="col-lg-4 col-12">
        <div class="small-box text-bg-primary">
            <div class="inner">
                <h3>Reports</h3>
                <p>Financial Statements</p>
            </div>
            <div class="icon">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </div>
            <a href="<?= BASE_URL ?>finance/index.php" class="small-box-footer">Generate <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <h3 class="card-title">Financial Overview</h3>
    </div>
    <div class="card-body">
        <p>Manage budgets, approve expenses, and audit financial records.</p>
    </div>
</div>
