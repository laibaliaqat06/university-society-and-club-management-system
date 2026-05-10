
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
            <a href="<?= BASE_URL ?>modules/finance/index.php" class="small-box-footer">View Funds <i class="bi bi-arrow-right-circle"></i></a>
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
            <a href="<?= BASE_URL ?>modules/finance/approve_events.php" class="small-box-footer">Review Budgets <i class="bi bi-arrow-right-circle"></i></a>
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
            <a href="<?= BASE_URL ?>modules/finance/index.php" class="small-box-footer">Generate <i class="bi bi-arrow-right-circle"></i></a>
        </div>
    </div>
</div>

<div class="card glass-card border-0 shadow-sm mt-4">
    <div class="card-header border-bottom border-white border-opacity-10 py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i> Cash Flow Analysis</h5>
        <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">Last 6 Months</div>
    </div>
    <div class="card-body p-4">
        <canvas id="cashFlowChart" style="height: 300px;"></canvas>
    </div>
</div>

<?php
// Fetch Income vs Expense Data
$incomeData = $pdo->query("SELECT DATE_FORMAT(record_date, '%b') as month, SUM(amount) as total FROM finance_records WHERE type='income' AND status='approved' GROUP BY month ORDER BY record_date ASC LIMIT 6")->fetchAll();
$expenseData = $pdo->query("SELECT DATE_FORMAT(record_date, '%b') as month, SUM(amount) as total FROM finance_records WHERE type='expense' AND status='approved' GROUP BY month ORDER BY record_date ASC LIMIT 6")->fetchAll();

$financeLabels = [];
$incomeValues = [];
$expenseValues = [];

// Initialize labels from income/expense or just empty
foreach($incomeData as $i) { $financeLabels[$i['month']] = $i['month']; $incomeValues[$i['month']] = $i['total']; }
foreach($expenseData as $e) { $financeLabels[$e['month']] = $e['month']; $expenseValues[$e['month']] = $e['total']; }

$financeLabels = array_values($financeLabels);
$finalIncomes = [];
$finalExpenses = [];
foreach($financeLabels as $m) {
    $finalIncomes[] = $incomeValues[$m] ?? 0;
    $finalExpenses[] = $expenseValues[$m] ?? 0;
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('cashFlowChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($financeLabels) ?>,
            datasets: [
                {
                    label: 'Income',
                    data: <?= json_encode($finalIncomes) ?>,
                    backgroundColor: '#10b981',
                    borderRadius: 5
                },
                {
                    label: 'Expense',
                    data: <?= json_encode($finalExpenses) ?>,
                    backgroundColor: '#ef4444',
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#ffffff90' } }
            },
            scales: {
                y: { grid: { color: '#ffffff10' }, ticks: { color: '#ffffff50' } },
                x: { grid: { display: false }, ticks: { color: '#ffffff50' } }
            }
        }
    });
});
</script>

