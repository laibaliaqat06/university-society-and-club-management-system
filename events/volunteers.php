<?php
require_once '../includes/header.php';

$role = $_SESSION['role'] ?? 'guest';
$userId = $_SESSION['user_id'] ?? 0;

if (!isset($_SESSION['user_id'])) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Please log in.</div></div>");
}

$isAdmin = in_array($role, ['super_admin', 'society_admin', 'event_manager']);

// Handle Volunteer Application (Member Action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_event_id']) && !$isAdmin) {
    $applyEventId = (int)$_POST['apply_event_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO event_volunteer_apps (event_id, user_id) VALUES (?, ?)");
        $stmt->execute([$applyEventId, $userId]);
        $successMsg = "Successfully applied as a volunteer!";
    } catch (PDOException $e) {
        $errorMsg = "You have already applied for this event.";
    }
}

// Handle Application Management (Admin Action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id']) && isset($_POST['status']) && $isAdmin) {
    $appId = (int)$_POST['app_id'];
    $status = $_POST['status'];
    if (in_array($status, ['selected', 'rejected', 'pending'])) {
        $stmt = $pdo->prepare("UPDATE event_volunteer_apps SET status = ? WHERE id = ?");
        $stmt->execute([$status, $appId]);
        $successMsg = "Applicant status updated.";
    }
}

// Fetch Data
if ($isAdmin) {
    // Admins see events they manage and the applications for them
    $query = "SELECT e.id, e.title, e.event_date, e.volunteers_needed, 
              (SELECT COUNT(*) FROM event_volunteer_apps WHERE event_id = e.id) as total_apps,
              (SELECT COUNT(*) FROM event_volunteer_apps WHERE event_id = e.id AND status = 'selected') as selected_apps
              FROM events e WHERE e.volunteers_needed > 0 ";
    $params = [];
    if ($role === 'society_admin') {
        $query .= " AND e.club_id IN (SELECT id FROM clubs WHERE created_by = ?) ";
        $params[] = $userId;
    } elseif ($role === 'event_manager') {
        $query .= " AND e.created_by = ? ";
        $params[] = $userId;
    }
    $query .= " ORDER BY e.event_date DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $managedEvents = $stmt->fetchAll();

    // If an event is selected for viewing applications
    $viewEventId = $_GET['view_event'] ?? ($managedEvents[0]['id'] ?? 0);
    $applications = [];
    if ($viewEventId) {
        $appQuery = "SELECT a.*, u.name, u.email FROM event_volunteer_apps a JOIN users u ON a.user_id = u.id WHERE a.event_id = ? ORDER BY a.applied_at DESC";
        $appStmt = $pdo->prepare($appQuery);
        $appStmt->execute([$viewEventId]);
        $applications = $appStmt->fetchAll();
    }

} else {
    // Members see available opportunities
    $query = "SELECT e.*, c.name as club_name, 
              (SELECT status FROM event_volunteer_apps WHERE event_id = e.id AND user_id = ?) as my_status
              FROM events e 
              JOIN clubs c ON e.club_id = c.id
              WHERE e.volunteers_needed > 0 AND e.event_date > NOW() 
              ORDER BY e.event_date ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $opportunities = $stmt->fetchAll();
}

?>

<div class="app-content-header position-relative overflow-hidden mb-5 py-5" style="border-radius: 0 0 40px 40px;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&q=80&w=1200') center/cover no-repeat; filter: brightness(0.3) saturate(1.2);"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-success px-3 py-2 mb-3">Community Service</span>
                <h1 class="display-3 fw-bold text-white mb-3">Volunteer Management</h1>
                <p class="lead text-white-50 mb-0"><?= $isAdmin ? 'Manage volunteer applications for your events.' : 'Discover and apply for volunteer opportunities on campus.' ?></p>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container pb-5">
        <?php if(isset($successMsg)): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= $successMsg ?></div>
        <?php endif; ?>
        <?php if(isset($errorMsg)): ?>
            <div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i><?= $errorMsg ?></div>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        
        <!-- ADMIN DASHBOARD -->
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card glass-card border-0 shadow-lg mb-4">
                    <div class="card-header border-bottom border-white border-opacity-10 py-3">
                        <h5 class="mb-0 fw-bold">Events Requiring Volunteers</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if(empty($managedEvents)): ?>
                            <div class="p-3 text-muted">No events currently require volunteers. Edit an event to set 'Volunteers Needed'.</div>
                        <?php else: ?>
                            <?php foreach($managedEvents as $me): ?>
                                <a href="?view_event=<?= $me['id'] ?>" class="list-group-item list-group-item-action <?= $me['id'] == $viewEventId ? 'active bg-primary text-white border-primary' : '' ?> d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($me['title']) ?></h6>
                                        <small class="<?= $me['id'] == $viewEventId ? 'text-white-50' : 'text-muted' ?>"><?= date('M d', strtotime($me['event_date'])) ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill bg-light text-dark shadow-sm"><?= $me['selected_apps'] ?> / <?= $me['volunteers_needed'] ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <?php if($viewEventId): ?>
                <div class="card glass-card border-0 shadow-lg">
                    <div class="card-header border-bottom border-white border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Applicants for Selected Event</h5>
                        <a href="view.php?id=<?= $viewEventId ?>" class="btn btn-sm btn-outline-primary" target="_blank">View Event <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Applicant Data</th>
                                        <th>Applied On</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($applications)): ?>
                                        <tr><td colspan="4" class="text-center py-5 text-muted">No applications received yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($applications as $app): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold"><?= htmlspecialchars($app['name']) ?></div>
                                                    <div class="small text-muted"><?= htmlspecialchars($app['email']) ?></div>
                                                </td>
                                                <td><small class="text-muted"><?= date('M d, Y h:i A', strtotime($app['applied_at'])) ?></small></td>
                                                <td>
                                                    <?php 
                                                        $badgeClass = 'bg-warning text-dark';
                                                        if($app['status'] == 'selected') $badgeClass = 'bg-success';
                                                        if($app['status'] == 'rejected') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= ucfirst($app['status']) ?></span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto bg-dark text-white border-secondary" onchange="this.form.submit()">
                                                            <option value="pending" <?= $app['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="selected" <?= $app['status'] == 'selected' ? 'selected' : '' ?>>Selected</option>
                                                            <option value="rejected" <?= $app['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                        </select>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="card glass-card border-0 shadow-sm d-flex justify-content-center align-items-center p-5 text-center text-muted" style="min-height: 300px;">
                        <div>
                            <i class="bi bi-hand-index-thumb display-1 opacity-25 mb-3"></i>
                            <h4>Select an event</h4>
                            <p>Choose an event from the list to view its volunteer applications.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php else: ?>
        
        <!-- MEMBER DASHBOARD -->
        <h4 class="fw-bold mb-4">Upcoming Volunteer Opportunities</h4>
        <div class="row g-4">
            <?php if(empty($opportunities)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted opacity-25 mb-3"></i>
                    <h4 class="text-muted">No volunteer opportunities available right now.</h4>
                    <p class="text-muted">Check back later for upcoming events!</p>
                </div>
            <?php else: ?>
                <?php foreach($opportunities as $opp): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative hover-lift transition-all">
                            <?php if($opp['my_status'] == 'selected'): ?>
                                <div class="position-absolute top-0 end-0 bg-success text-white px-3 py-1 m-3 rounded-pill shadow-sm" style="z-index: 10;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Selected
                                </div>
                            <?php elseif($opp['my_status'] == 'pending'): ?>
                                <div class="position-absolute top-0 end-0 bg-warning text-dark px-3 py-1 m-3 rounded-pill shadow-sm" style="z-index: 10;">
                                    <i class="bi bi-hourglass-split me-1"></i> Pending
                                </div>
                            <?php elseif($opp['my_status'] == 'rejected'): ?>
                                <div class="position-absolute top-0 end-0 bg-danger text-white px-3 py-1 m-3 rounded-pill shadow-sm" style="z-index: 10;">
                                    <i class="bi bi-x-circle-fill me-1"></i> Not Selected
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body p-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill"><?= htmlspecialchars($opp['club_name']) ?></span>
                                <h4 class="fw-bold mb-2"><?= htmlspecialchars($opp['title']) ?></h4>
                                <div class="text-muted small mb-3">
                                    <div class="mb-1"><i class="bi bi-calendar-event me-2 text-primary"></i><?= date('F d, Y - h:i A', strtotime($opp['event_date'])) ?></div>
                                    <div><i class="bi bi-geo-alt me-2 text-danger"></i><?= htmlspecialchars($opp['location']) ?></div>
                                </div>
                                <div class="d-flex align-items-center mb-4 bg-light p-3 rounded-3">
                                    <div class="display-6 text-warning me-3"><i class="bi bi-people-fill"></i></div>
                                    <div>
                                        <div class="small fw-bold text-muted text-uppercase tracking-wider">Volunteers Needed</div>
                                        <div class="fs-5 fw-bold"><?= $opp['volunteers_needed'] ?> Spots Total</div>
                                    </div>
                                </div>
                                
                                <div class="d-grid mt-auto">
                                    <?php if(!$opp['my_status']): ?>
                                        <form method="POST">
                                            <input type="hidden" name="apply_event_id" value="<?= $opp['id'] ?>">
                                            <button type="submit" class="btn btn-premium w-100" onclick="return confirm('Are you sure you want to apply as a volunteer for this event?');">
                                                <i class="bi bi-hand-index-thumb me-2"></i> Apply to Volunteer
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="view.php?id=<?= $opp['id'] ?>" class="btn btn-outline-secondary w-100">View Event Details</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <style>
            .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
            .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
            .tracking-wider { letter-spacing: 0.05em; }
        </style>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
