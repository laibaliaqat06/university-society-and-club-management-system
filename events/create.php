<?php
require_once '../includes/header.php';

// Fetch Clubs (if super admin, show all; otherwise show user's club)
$clubId = null;
if ($_SESSION['role'] == 'super_admin') {
    $clubs = $pdo->query("SELECT * FROM clubs")->fetchAll();
} else {
    // Ideally query user's club. For now, fetch all or just one.
    $clubs = $pdo->query("SELECT * FROM clubs")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO events (club_id, title, description, event_date, location, volunteers_needed, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['club_id'],
            trim($_POST['title']),
            $_POST['description'],
            $_POST['date'],
            $_POST['location'],
            (int)$_POST['volunteers_needed'],
            $_SESSION['user_id']
        ]);
        echo "<script>alert('Event created!'); window.location.href='manage.php';</script>";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Create New Event</h3>
    </div>
    <div class="card-body">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Club / Society</label>
                    <select name="club_id" class="form-select" required>
                        <?php foreach($clubs as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Event Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date & Time</label>
                    <input type="datetime-local" name="date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Volunteers Needed</label>
                    <input type="number" name="volunteers_needed" class="form-control" value="0" min="0">
                </div>
                <div class="col-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create Event</button>
                    <a href="manage.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
