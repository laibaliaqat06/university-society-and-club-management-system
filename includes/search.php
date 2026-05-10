<?php
require_once 'session.php';
require_once 'db.php';

if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    exit;
}

$query = '%' . $_GET['query'] . '%';

// Search Clubs
$stmt = $pdo->prepare("SELECT id, name, category, logo FROM clubs WHERE name LIKE ? OR description LIKE ? LIMIT 5");
$stmt->execute([$query, $query]);
$clubs = $stmt->fetchAll();

// Search Events
$stmt = $pdo->prepare("SELECT id, title, event_date, location FROM events WHERE title LIKE ? OR description LIKE ? LIMIT 5");
$stmt->execute([$query, $query]);
$events = $stmt->fetchAll();

// Search Users
$stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE name LIKE ? OR email LIKE ? LIMIT 5");
$stmt->execute([$query, $query]);
$users = $stmt->fetchAll();

if (empty($clubs) && empty($events) && empty($users)) {
    echo '<div class="p-3 text-white-50 text-center">No results found for "'.htmlspecialchars($_GET['query']).'"</div>';
    exit;
}

// Display Clubs
if ($clubs) {
    echo '<div class="p-2 border-bottom border-white border-opacity-10"><small class="text-primary fw-bold text-uppercase px-2" style="font-size: 0.65rem;">Societies</small></div>';
    foreach ($clubs as $c) {
        $logo = !empty($c['logo']) ? BASE_URL . $c['logo'] : BASE_URL . 'assets/img/default-logo.png';
        echo '<a href="'.BASE_URL.'clubs/view.php?id='.$c['id'].'" class="d-flex align-items-center p-2 text-white text-decoration-none hover-bg-white-10">
                <img src="'.$logo.'" class="rounded-circle me-3" style="width: 30px; height: 30px; object-fit: cover;">
                <div>
                    <div class="small fw-bold">'.htmlspecialchars($c['name']).'</div>
                    <div class="extra-small text-white-50">'.htmlspecialchars($c['category']).'</div>
                </div>
              </a>';
    }
}

// Display Events
if ($events) {
    echo '<div class="p-2 border-bottom border-white border-opacity-10 mt-2"><small class="text-success fw-bold text-uppercase px-2" style="font-size: 0.65rem;">Events</small></div>';
    foreach ($events as $e) {
        echo '<a href="'.BASE_URL.'events/view.php?id='.$e['id'].'" class="d-flex align-items-center p-2 text-white text-decoration-none hover-bg-white-10">
                <div class="bg-success bg-opacity-10 rounded text-success p-2 me-3"><i class="bi bi-calendar-event"></i></div>
                <div>
                    <div class="small fw-bold">'.htmlspecialchars($e['title']).'</div>
                    <div class="extra-small text-white-50">'.date('M d', strtotime($e['event_date'])).' • '.htmlspecialchars($e['location']).'</div>
                </div>
              </a>';
    }
}

// Display Users
if ($users) {
    echo '<div class="p-2 border-bottom border-white border-opacity-10 mt-2"><small class="text-warning fw-bold text-uppercase px-2" style="font-size: 0.65rem;">People</small></div>';
    foreach ($users as $u) {
        echo '<a href="'.BASE_URL.'profile.php?id='.$u['id'].'" class="d-flex align-items-center p-2 text-white text-decoration-none hover-bg-white-10">
                <div class="bg-warning bg-opacity-10 rounded-circle text-warning d-flex align-items-center justify-content-center me-3" style="width: 30px; height: 30px; font-weight: bold;">'.strtoupper(substr($u['name'], 0, 1)).'</div>
                <div>
                    <div class="small fw-bold">'.htmlspecialchars($u['name']).'</div>
                    <div class="extra-small text-white-50">'.ucfirst(str_replace('_', ' ', $u['role'])).'</div>
                </div>
              </a>';
    }
}
?>

<style>
    .hover-bg-white-10:hover { background-color: rgba(255, 255, 255, 0.05); }
    .extra-small { font-size: 0.7rem; }
</style>
