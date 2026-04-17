<?php
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/Notifications.php';

$unreadCount = 0;
$unreadNotifs = [];
if (isset($_SESSION['user_id'])) {
    $notifObj = new Notifications($pdo);
    $unreadCount = $notifObj->countUnread($_SESSION['user_id']);
    $unreadNotifs = $notifObj->getUnread($_SESSION['user_id'], 5);
}// 1. Fetch System Settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM system_settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// 2. Identify Current Page & Security Check
$current_url = substr($_SERVER['SCRIPT_NAME'], strlen('/universal/')); // Adjust offset
// Clean URL for DB matching (assuming DB stores relative paths)
$db_url_match = $current_url; 
// If your script is in a folder, the DB url should match "dashboards/super_admin/file.php"

// Fetch Page Info - Use exact match for stability
$pageStmt = $pdo->prepare("SELECT * FROM sys_pages WHERE page_url = ? LIMIT 1");
if (empty($current_url) || $current_url === 'index.php') {
    $pageStmt->execute(['index.php']);
} else {
    $pageStmt->execute([$current_url]);
}
$currentPageData = $pageStmt->fetch();

$pageTitle = $currentPageData['page_name'] ?? 'Dashboard';
$pageId = $currentPageData['id'] ?? 0;

// 3. Security Access Check (The Gatekeeper)
$is_public = in_array(basename($_SERVER['PHP_SELF']), $public_pages);

if ($pageId > 0 && $_SESSION['role'] !== 'super_admin' && !$is_public) {
    $accessStmt = $pdo->prepare("SELECT * FROM role_access WHERE role_key = ? AND page_id = ?");
    $accessStmt->execute([$_SESSION['role'], $pageId]);
    if ($accessStmt->rowCount() == 0) {
        die('<div class="alert alert-danger m-5">⛔ Access Denied: You do not have permission to view this page.</div>');
    }
}

// 4. Breadcrumb Logic (Recursive Upwards)
$breadcrumbs = [];
if ($currentPageData) {
    $crumbId = $currentPageData['id'];
    while($crumbId != 0) {
        $crumbStmt = $pdo->prepare("SELECT id, parent_id, page_name, page_url FROM sys_pages WHERE id = ?");
        $crumbStmt->execute([$crumbId]);
        $crumb = $crumbStmt->fetch();
        array_unshift($breadcrumbs, $crumb); // Add to beginning
        $crumbId = $crumb['parent_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club & Society Management</title>
    
    <script>
        // Immediately check local storage to prevent "White Flash"
        const storedTheme = localStorage.getItem('theme');
        if (storedTheme) {
            document.documentElement.setAttribute('data-bs-theme', storedTheme);
        } else {
            // Default to system preference if no choice made
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', systemTheme);
        }
    </script>

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap-icons.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/adminlte.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/premium.css?v=<?= time() ?>" />
    
    <style> 
        .app-brand-logo { height: 30px; width: auto; } 
        .user-image { width: 30px; height: 30px; object-fit: cover; }
    </style>
</head>
<body class="layout-fixed <?= $_SESSION['role'] === 'student' ? 'sidebar-collapse' : 'sidebar-expand-lg' ?> bg-body-tertiary">
<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-body shadow-sm border-bottom border-white border-opacity-10">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <?php if($_SESSION['role'] !== 'guest'): ?>
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a> </li>
                <?php endif; ?>
                <li class="nav-item d-none d-md-block"> <a href="<?= BASE_URL ?>" class="nav-link fw-bold fs-5"><?= $pageTitle ?></a> </li>
                
                <?php if ($_SESSION['role'] === 'student'): ?>
                    <li class="nav-item d-none d-md-block"> <a href="<?= BASE_URL ?>clubs" class="nav-link">Societies</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="<?= BASE_URL ?>events" class="nav-link">Events</a> </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                 <li class="nav-item">
                    <button class="btn btn-link nav-link" id="theme-toggle" type="button">
                        <i class="bi bi-sun-fill" id="theme-icon"></i>
                    </button>
                </li>
                <?php if($_SESSION['role'] !== 'guest'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#">
                        <i class="bi bi-bell-fill"></i>
                        <?php if($unreadCount > 0): ?>
                            <span class="badge text-bg-danger navbar-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-sm">
                        <span class="dropdown-item dropdown-header"><?= $unreadCount ?> Unread Notifications</span>
                        <div class="dropdown-divider"></div>
                        <?php if(empty($unreadNotifs)): ?>
                            <a href="#" class="dropdown-item text-center text-muted">No new notifications</a>
                        <?php else: ?>
                            <?php foreach($unreadNotifs as $n): ?>
                                <a href="<?= BASE_URL ?>notifications/read_all.php?id=<?= $n['notif_id'] ?>&redir=1" class="dropdown-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="bi bi-megaphone-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="dropdown-item-title fw-bold mb-0">
                                                <?= htmlspecialchars(substr($n['title'], 0, 20)) ?>...
                                            </h6>
                                            <p class="fs-7 text-secondary mb-0">
                                                <i class="bi bi-clock-history me-1"></i> <?= date('M d, H:i', strtotime($n['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>notifications/read_all.php" class="dropdown-item dropdown-footer text-center">Mark all as read</a>
                    </div>
                </li>
                <?php endif; ?>

                <?php if($_SESSION['role'] !== 'guest'): ?>
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <?php 
                            $avatar = !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : BASE_URL.'assets/img/avatar.png';
                            if (!empty($_SESSION['avatar']) && strpos($_SESSION['avatar'], 'http') !== 0) {
                                $avatar = BASE_URL . $_SESSION['avatar'];
                            }
                        ?>
                        <img src="<?= $avatar ?>" class="user-image rounded-circle shadow" alt="User Image">
                        <span class="d-none d-md-inline ms-1"><?= htmlspecialchars($_SESSION['name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <li class="user-header text-bg-primary">
                            <img src="<?= $avatar ?>" class="rounded-circle shadow" alt="User Image">
                            <p>
                                <?= htmlspecialchars($_SESSION['name']) ?>
                                <small><?= ucfirst(str_replace('_', ' ', $_SESSION['role'])) ?></small>
                            </p>
                        </li>
                        <li class="user-footer"> 
                            <a href="<?= BASE_URL ?>profile.php" class="btn btn-default btn-flat">Profile</a>
                            <a href="<?= BASE_URL ?>logout.php" class="btn btn-default btn-flat float-end">Sign out</a> 
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>login.php" class="btn btn-premium px-4 rounded-pill ms-2">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <?php if($_SESSION['role'] !== 'guest') include 'sidebar.php'; ?>
    
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6"><h3 class="mb-0"><?= $pageTitle ?></h3></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
                            <?php foreach($breadcrumbs as $b): ?>
                                <li class="breadcrumb-item <?= ($b['id'] == $pageId) ? 'active' : '' ?>">
                                    <?= htmlspecialchars($b['page_name']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">