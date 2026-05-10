<?php
require_once '../../includes/header.php';

// Handle Transaction
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $club_id = $_POST['club_id'] ?? null; // Optional: bind to a club if needed, or null for general
    // For now, let's assume it's general or we pick the user's club if they are a society admin
    
    if (!$club_id && $_SESSION['role'] == 'society_admin') {
         $cStmt = $pdo->prepare("SELECT id FROM clubs WHERE created_by = ?");
         $cStmt->execute([$_SESSION['user_id']]);
         $club_id = $cStmt->fetchColumn();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO finance_records (club_id, amount, type, description, status, created_by) VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([$club_id, $amount, $type, $description, $_SESSION['user_id']]);
        echo "<script>alert('Transaction recorded successfully! It is now pending approval.'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
        echo "<script>alert('$error');</script>";
    }
}
?>

<div class="card card-primary card-outline">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Manage Finances</h3>
        <a href="<?= BASE_URL ?>includes/export_csv.php?action=export_finance" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Records
        </a>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Transaction Type</label>
                    <select name="type" class="form-select">
                        <option value="income">Income / Deposit</option>
                        <option value="expense">Expense / Withdrawal</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Amount</label>
                    <input type="number" name="amount" class="form-control" placeholder="0.00" required>
                </div>
                <div class="col-12 mb-3">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Record Transaction</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

