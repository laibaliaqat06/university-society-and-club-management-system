<?php
require_once '../core/session.php';
require_once '../core/Clubs.php';
require_once '../core/Events.php';

$clubs = new Clubs($pdo);
$events = new Events($pdo);

$clubId = $_GET['id'] ?? 0;
// Handle Join & Create Event logic (kept same as before, simplified for brevity in this view update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_club'])) {
    $res = $clubs->joinClub($_SESSION['user_id'], $clubId);
    if (!$res) {
        die("<div style='background:#fff; color:#000; padding: 20px;'><h1>Error Joining Club</h1><p>Join club function returned false. Are you already a member? User ID: ".$_SESSION['user_id']."</p></div>");
    }
    header("Location: view.php?id=" . $clubId); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_club'])) {
    $res = $clubs->removeMember($_SESSION['user_id'], $clubId);
    if (!$res) {
        die("<div style='background:#fff; color:#000; padding: 20px;'><h1>Error Leaving Club</h1><p>Leave club function returned false. User ID: ".$_SESSION['user_id']."</p></div>");
    }
    header("Location: view.php?id=" . $clubId); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $budget = floatval($_POST['budget_amount'] ?? 0);
    $details = $_POST['budget_details'] ?? '';
    $events->create($clubId, $_POST['title'], $_POST['description'], $_POST['date'], $_POST['location'], $_SESSION['user_id'], $budget, $details);
    echo "<script>alert('Event proposal submitted! It will be visible after Admin and Finance approval.'); window.location.href='view.php?id=$clubId';</script>";
    exit;
}

$club = $clubs->getById($clubId);
require_once '../includes/header.php';

if (!$club) { echo '<div class="alert alert-danger m-4">Club not found.</div>'; require_once '../includes/footer.php'; exit; }

$isMember = $clubs->isMember($_SESSION['user_id'], $clubId);
$members = $clubs->getMembers($clubId);
$clubEvents = $events->getByClub($clubId);
$gallery = $pdo->query("SELECT * FROM club_gallery WHERE club_id = $clubId ORDER BY created_at DESC")->fetchAll();

// Helper to convert comma-separated user IDs to names
// Helper to convert comma-separated user IDs to names or handle descriptive text
function getUserNamesByIds($pdo, $idString) {
    if (empty($idString)) return 'Not Assigned';
    
    // Check if the string contains only numbers and commas
    if (preg_match('/^[0-9, ]+$/', $idString)) {
        $ids = array_filter(array_map('trim', explode(',', $idString)));
        if (empty($ids)) return 'Not Assigned';
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id IN ($placeholders)");
        $stmt->execute(array_values($ids));
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($users)) return 'Unknown User(s)';
        
        $links = array_map(function($u) {
            return '<a href="' . BASE_URL . 'profile.php?id=' . $u['id'] . '" class="text-primary text-decoration-none fw-bold"><i class="bi bi-person-circle me-1"></i>' . htmlspecialchars($u['name']) . '</a>';
        }, $users);
        
        return implode(', ', $links);
    }
    
    // If it's descriptive text, return it formatted
    return '<span class="text-white-50 italic">' . nl2br(htmlspecialchars($idString)) . '</span>';
}
?>

<!-- Premium Hero Section -->
<?php 
    $cover = !empty($club['cover_image']) ? $club['cover_image'] : BASE_URL.'assets/img/default-cover.jpg';
    if (!empty($club['cover_image']) && strpos($club['cover_image'], 'http') !== 0) {
        $cover = BASE_URL . $club['cover_image'];
    }
    $logo = !empty($club['logo']) ? $club['logo'] : BASE_URL.'assets/img/default-logo.png';
    if (!empty($club['logo']) && strpos($club['logo'], 'http') !== 0) {
        $logo = BASE_URL . $club['logo'];
    }
?>
<div class="position-relative hero-glow mb-5 overflow-hidden shadow-lg" style="height: 400px; border-radius: 0 0 30px 30px; background: #1a1a1a;">
    <div class="position-absolute w-100 h-100" style="background: url('<?= htmlspecialchars($cover) ?>') center/cover no-repeat; filter: brightness(0.4) saturate(1.2);"></div>
    <div class="container h-100 position-relative d-flex align-items-end pb-5 mt-n5">
        <div class="row w-100 align-items-center mb-4">
            <div class="col-md-2 text-center text-md-start mb-3 mb-md-0">
                <img src="<?= htmlspecialchars($logo) ?>" class="rounded-circle border border-white border-4 shadow-lg p-1 bg-white" style="width: 150px; height: 150px; object-fit: cover;">
            </div>
            <div class="col-md-7 text-center text-md-start">
                <span class="badge bg-primary px-3 py-2 mb-2"><?= htmlspecialchars($club['category'] ?? 'General') ?></span>
                <h1 class="display-3 fw-bold text-white mb-2"><?= htmlspecialchars($club['name']) ?></h1>
                <p class="lead text-white-50"><i class="bi bi-people-fill me-2 text-primary"></i> <span class="text-white fw-bold"><?= count($members) ?></span> <span class="small">Active Members</span></p>
            </div>
            <div class="col-md-3 text-center text-md-end">
                <?php if ($_SESSION['role'] === 'super_admin' || $_SESSION['user_id'] == $club['created_by']): ?>
                    <a href="edit.php?id=<?= $club['id'] ?>" class="btn btn-warning btn-lg me-2 mb-2"><i class="bi bi-pencil"></i></a>
                    <a href="members.php?id=<?= $club['id'] ?>" class="btn btn-primary btn-lg mb-2"><i class="bi bi-people"></i></a>
                <?php endif; ?>

                <?php if (!$isMember): ?>
                    <button type="button" class="btn btn-premium btn-lg w-100" data-bs-toggle="modal" data-bs-target="#joinSocietyModal">Join Society</button>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-danger btn-lg w-100" data-bs-toggle="modal" data-bs-target="#leaveSocietyModal">Leave Society</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container shadow-none">
        <div class="row g-4">
            <!-- Left Column: Info & Events -->
            <div class="col-lg-8">
                <!-- Stats Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center">
                            <h2 class="fw-bold mb-0 text-white"><?= count($members) ?></h2>
                            <p class="text-white-50 small mb-0">Total Members</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center">
                            <h2 class="fw-bold mb-0 text-white"><?= count($clubEvents) ?></h2>
                            <p class="text-white-50 small mb-0">Events Hosted</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center">
                            <h2 class="fw-bold mb-0 text-white"><?= count($gallery) ?></h2>
                            <p class="text-white-50 small mb-0">Gallery Items</p>
                        </div>
                    </div>
                </div>

                <!-- Mission & Vision -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100">
                            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-bullseye me-2"></i> Our Mission</h5>
                            <p class="text-white-50"><?= nl2br(htmlspecialchars($club['mission'] ?? 'To foster a community of excellence and innovation within our university through collaborative growth and shared passion.')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 h-100">
                            <h5 class="fw-bold mb-3 text-success"><i class="bi bi-eye me-2"></i> Our Vision</h5>
                            <p class="text-white-50"><?= nl2br(htmlspecialchars($club['vision'] ?? 'To become the leading campus society recognized for student leadership and transformative impact on university culture.')) ?></p>
                        </div>
                    </div>
                </div>

                <!-- About Us -->
                <div class="glass-card mb-4 p-4">
                    <h5 class="fw-bold mb-3">About Us</h5>
                    <p class="lead text-white-50 mb-0"><?= nl2br(htmlspecialchars($club['description'])) ?></p>
                </div>

                <!-- Membership Guidelines -->
                <?php if(!empty($club['joining_rules']) || !empty($club['exit_rules'])): ?>
                <div class="glass-card mb-4 p-4">
                    <h5 class="fw-bold mb-4 border-bottom border-white border-opacity-10 pb-2">Membership Guidelines</h5>
                    <div class="row g-4">
                        <?php if(!empty($club['joining_rules'])): ?>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success"><i class="bi bi-door-open me-2"></i> Joining the Society</h6>
                            <p class="text-white-50 small mb-0"><?= nl2br(htmlspecialchars($club['joining_rules'])) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($club['exit_rules'])): ?>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-danger"><i class="bi bi-door-closed me-2"></i> Exiting the Society</h6>
                            <p class="text-white-50 small mb-0"><?= nl2br(htmlspecialchars($club['exit_rules'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Gallery -->
                <div class="glass-card mb-4 p-4">
                    <h5 class="fw-bold mb-3">Gallery</h5>
                    <?php if($gallery): ?>
                        <div class="row g-3">
                            <?php foreach($gallery as $img): 
                                $imgSrc = $img['image_url'];
                                if (strpos($imgSrc, 'http') !== 0) {
                                    $imgSrc = BASE_URL . $imgSrc;
                                }
                            ?>
                                <div class="col-4 col-md-3">
                                    <div class="overflow-hidden rounded shadow-sm" style="height: 120px; background: rgba(255,255,255,0.05);">
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" onerror="this.parentElement.style.display='none'">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 opacity-50">
                            <i class="bi bi-images display-4 d-block mb-2 text-white-50"></i>
                            <p class="text-white-50">No images yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Events Section -->
                <div class="glass-card mb-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Upcoming Events</h5>
                        <?php if($_SESSION['role'] === 'super_admin' || $_SESSION['user_id'] == $club['created_by']): ?>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createEventModal"><i class="bi bi-plus"></i></button>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($clubEvents)): ?>
                        <div class="text-center py-4 opacity-50">
                            <i class="bi bi-calendar-x display-4 d-block mb-2 text-white-50"></i>
                            <p class="text-white-50">No events scheduled.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                        <?php foreach ($clubEvents as $event): ?>
                            <div class="col-md-6">
                                <div class="p-3 rounded bg-white bg-opacity-5 border border-white border-opacity-10 h-100 d-flex flex-column">
                                    <h6 class="fw-bold text-primary mb-1"><?= htmlspecialchars($event['title']) ?></h6>
                                    <div class="small text-white-50 mb-2"><i class="bi bi-calendar3 me-1"></i> <?= date('M d, Y', strtotime($event['event_date'])) ?></div>
                                    <div class="small text-white-50 mb-3"><i class="bi bi-geo-alt me-1 text-danger"></i> <?= htmlspecialchars($event['location']) ?></div>
                                    
                                    <div class="mt-auto">
                                        <form method="POST" action="<?= BASE_URL ?>index.php">
                                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                            <button type="submit" name="rsvp_event" class="btn btn-sm btn-premium w-100 rounded-pill">Enroll Now</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Members & Contact -->
            <div class="col-lg-4">
                <div class="glass-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Contact & Support</h5>
                    <div class="mb-3">
                        <label class="text-white-50 small d-block">Official Email</label>
                        <span class="text-white"><i class="bi bi-envelope text-primary me-2"></i> <?= htmlspecialchars($club['contact_email'] ?? 'Not available') ?></span>
                    </div>
                    <div class="mb-4">
                        <label class="text-white-50 small d-block">Phone Number</label>
                        <span class="text-white"><i class="bi bi-telephone text-success me-2"></i> <?= htmlspecialchars($club['contact_phone'] ?? 'Not available') ?></span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-light flex-grow-1"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-light flex-grow-1"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-light flex-grow-1"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>

                <!-- Unified Society Board -->
                <div class="glass-card mb-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <h5 class="fw-bold mb-0">Society Board</h5>
                        <?php if ($_SESSION['role'] === 'super_admin' || $_SESSION['user_id'] == $club['created_by']): ?>
                            <a href="members.php?id=<?= $club['id'] ?>" class="btn btn-xs btn-outline-primary p-1 px-2" style="font-size: 0.7rem;"><i class="bi bi-gear"></i> Manage</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="list-group list-group-flush bg-transparent">
                        <?php 
                        $boardMembers = array_filter($members, function($m) { return $m['role'] !== 'member'; });
                        if (empty($boardMembers)): ?>
                            <div class="text-white-50 small p-3 text-center border border-white border-opacity-10 rounded">
                                <i class="bi bi-info-circle d-block fs-4 mb-2"></i>
                                No board members assigned yet.
                            </div>
                        <?php else: ?>
                            <?php foreach ($boardMembers as $member): ?>
                                <div class="list-group-item bg-transparent border-white border-opacity-10 px-0 py-3 d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center text-white me-3 shadow-sm border border-primary border-opacity-25" style="width: 45px; height: 45px; font-weight: 600;">
                                        <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <a href="<?= BASE_URL ?>profile.php?id=<?= $member['id'] ?>" class="text-white small fw-bold text-decoration-none">
                                                <?= htmlspecialchars($member['name']) ?>
                                            </a>
                                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;"><?= ucfirst($member['role']) ?></span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="x-small text-white-50 text-decoration-none" style="font-size: 0.75rem;">
                                                <i class="bi bi-envelope me-1"></i> Contact
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Create Event Modal (Updated with Budget Workflow) -->
<div class="modal fade" id="createEventModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content glass-card border-0 shadow-lg">
            <input type="hidden" name="create_event" value="1">
            <div class="modal-header border-bottom border-white border-opacity-10">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-calendar-plus text-primary me-2"></i> Propose New Event</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                <div class="mb-3">
                    <label class="small text-white-50">Event Title</label>
                    <input type="text" name="title" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Annual Tech Symposium" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="small text-white-50">Event Date & Time</label>
                        <input type="datetime-local" name="date" class="form-control bg-dark border-secondary text-white" required>
                    </div>
                    <div class="col-6">
                        <label class="small text-white-50">Location</label>
                        <input type="text" name="location" class="form-control bg-dark border-secondary text-white" placeholder="Venue/Online" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small text-white-50">Event Description</label>
                    <textarea name="description" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="Explain the event goals..."></textarea>
                </div>
                
                <hr class="border-white border-opacity-10 my-4">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cash-stack me-2"></i> Finance & Budget Application</h6>
                
                <div class="mb-3">
                    <label class="small text-white-50">Estimated Budget (PKR / $)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-white">$</span>
                        <input type="number" step="0.01" name="budget_amount" class="form-control bg-dark border-secondary text-white" placeholder="0.00" required>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="small text-white-50">Budget Breakdown / Details</label>
                    <textarea name="budget_details" class="form-control bg-dark border-secondary text-white" rows="3" placeholder="List expenses like snacks, decorations, prizes..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top border-white border-opacity-10">
                <p class="small text-white-50 me-auto">Subject to Admin & Finance approval.</p>
                <button type="submit" class="btn btn-primary px-4 rounded-pill shadow">Submit Proposal</button>
            </div>
        </form>
    </div>
</div>

<!-- Join Society Modal -->
<div class="modal fade" id="joinSocietyModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="view.php?id=<?= $clubId ?>" method="POST" class="modal-content glass-card border-0 shadow-lg">
            <input type="hidden" name="join_club" value="1">
            <div class="modal-header border-bottom border-white border-opacity-10">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-door-open text-success me-2"></i> Join <?= htmlspecialchars($club['name']) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                <?php if(!empty($club['joining_rules'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold text-success mb-2">Please read the joining rules carefully:</h6>
                        <div class="p-3 bg-dark bg-opacity-50 rounded small text-white" style="max-height: 200px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1);">
                            <?= nl2br(htmlspecialchars($club['joining_rules'])) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-white-50">Are you sure you want to join this society?</p>
                <?php endif; ?>
                
                <div class="form-check mt-3">
                    <input class="form-check-input bg-dark border-secondary" type="checkbox" value="" id="agreeJoin" required>
                    <label class="form-check-label small text-white-50" for="agreeJoin">
                        I have read and agree to follow the rules and regulations of this society.
                    </label>
                </div>
            </div>
            <div class="modal-footer border-top border-white border-opacity-10">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success px-4">Confirm Join</button>
            </div>
        </form>
    </div>
</div>

<!-- Leave Society Modal -->
<div class="modal fade" id="leaveSocietyModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="view.php?id=<?= $clubId ?>" method="POST" class="modal-content glass-card border-0 shadow-lg">
            <input type="hidden" name="leave_club" value="1">
            <div class="modal-header border-bottom border-white border-opacity-10">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-box-arrow-right text-danger me-2"></i> Leave <?= htmlspecialchars($club['name']) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-white">
                <?php if(!empty($club['exit_rules'])): ?>
                    <div class="mb-4">
                        <h6 class="fw-bold text-danger mb-2">Important information about leaving:</h6>
                        <div class="p-3 bg-dark bg-opacity-50 rounded small text-white" style="max-height: 200px; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1);">
                            <?= nl2br(htmlspecialchars($club['exit_rules'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                <p class="text-white">Are you sure you want to leave this society? You will lose access to member resources and upcoming private events.</p>
                
                <div class="form-check mt-3">
                    <input class="form-check-input bg-dark border-secondary" type="checkbox" value="" id="agreeLeave" required>
                    <label class="form-check-label small text-danger" for="agreeLeave">
                        I understand the consequences and confirm I want to leave.
                    </label>
                </div>
            </div>
            <div class="modal-footer border-top border-white border-opacity-10">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger px-4">Confirm Leave</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
