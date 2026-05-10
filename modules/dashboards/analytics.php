<?php
// Note: This expects $pdo, $role, and $userId to be set by the parent dashboard
$stats = [
    'societies' => 0,
    'members' => 0,
    'events' => 0,
    'finance' => 0,
];

// Fetch Stats Based on Role
if ($role === 'super_admin') {
    $stats['societies'] = $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
    $stats['members'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['events'] = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $stats['finance'] = $pdo->query("SELECT SUM(CASE WHEN type='income' THEN amount ELSE -amount END) FROM finance_records WHERE status='approved'")->fetchColumn() ?? 0;
    
    // Monthly events data
    $monthlyStmt = $pdo->query("SELECT DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count FROM events GROUP BY month ORDER BY month ASC LIMIT 6");
    $monthlyData = $monthlyStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Category Distribution
    $categoryStmt = $pdo->query("SELECT category, COUNT(*) as count FROM clubs GROUP BY category");
    $categoryData = $categoryStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
} elseif ($role === 'society_admin') {
    $cStmt = $pdo->prepare("SELECT id FROM clubs WHERE created_by = ?");
    $cStmt->execute([$userId]);
    $clubId = $cStmt->fetchColumn();
    
    if ($clubId) {
        $stats['societies'] = 1;
        $stats['members'] = $pdo->prepare("SELECT COUNT(*) FROM club_memberships WHERE club_id = ? AND status='approved'");
        $stats['members']->execute([$clubId]);
        $stats['members'] = $stats['members']->fetchColumn();
        
        $stats['events'] = $pdo->prepare("SELECT COUNT(*) FROM events WHERE club_id = ?");
        $stats['events']->execute([$clubId]);
        $stats['events'] = $stats['events']->fetchColumn();
        
        $stats['finance'] = $pdo->prepare("SELECT SUM(CASE WHEN type='income' THEN amount ELSE -amount END) FROM finance_records WHERE club_id = ? AND status='approved'");
        $stats['finance']->execute([$clubId]);
        $stats['finance'] = $stats['finance']->fetchColumn() ?? 0;
        
        $monthlyStmt = $pdo->prepare("SELECT DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count FROM events WHERE club_id = ? GROUP BY month ORDER BY month ASC LIMIT 6");
        $monthlyStmt->execute([$clubId]);
        $monthlyData = $monthlyStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } else {
        $monthlyData = [];
        $categoryData = [];
    }
}
?>

<!-- Analytics Section -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <h4 class="fw-bold mb-3"><i class="bi bi-graph-up pe-2 text-primary"></i> Analytical Overview</h4>
    </div>
    
    <div class="col-md-8">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header border-0 bg-transparent pt-4 pb-0">
                <h5 class="fw-bold mb-0">Event Timeline (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="eventsChart" style="min-height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <?php if(isset($categoryData)): ?>
    <div class="col-md-4">
        <div class="card glass-card border-0 shadow-sm h-100">
            <div class="card-header border-0 bg-transparent pt-4 pb-0">
                <h5 class="fw-bold mb-0">Society Distribution</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="categoryChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = '#888';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Monthly Events Bar Chart
    const ctxEvents = document.getElementById('eventsChart');
    if(ctxEvents) {
        new Chart(ctxEvents, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($monthlyData ?? [])) ?>,
                datasets: [{
                    label: 'Events Held',
                    data: <?= json_encode(array_values($monthlyData ?? [])) ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Category Pie Chart
    <?php if(isset($categoryData)): ?>
    const ctxCategory = document.getElementById('categoryChart');
    if(ctxCategory) {
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($categoryData)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($categoryData)) ?>,
                    backgroundColor: [
                        '#0d6efd', '#6610f2', '#6f42c1', '#d63384', 
                        '#dc3545', '#fd7e14', '#ffc107', '#198754'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
