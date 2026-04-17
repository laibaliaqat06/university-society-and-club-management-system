<?php
require_once '../includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    die("Event not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, volunteers_needed=? WHERE id=?");
        $stmt->execute([
            trim($_POST['title']),
            $_POST['description'],
            $_POST['date'],
            $_POST['location'],
            (int)$_POST['volunteers_needed'],
            $id
        ]);
        echo "<script>alert('Event updated!'); window.location.href='manage.php';</script>";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">Edit Event: <?= htmlspecialchars($event['title']) ?></h3>
    </div>
    <div class="card-body">
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Event Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Date & Time</label>
                    <input type="datetime-local" name="date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($event['event_date'])) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Volunteers Needed</label>
                    <input type="number" name="volunteers_needed" class="form-control" value="<?= (int)$event['volunteers_needed'] ?>" min="0">
                </div>
                <div class="col-12 mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($event['description']) ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-warning">Update Event</button>
                    <a href="manage.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
