<?php 
require_once 'includes/header.php'; 

// Role-Based Dashboard Loader
$role = $_SESSION['role'] ?? 'guest';

switch ($role) {
    case 'super_admin':
        include 'dashboards/super_admin.php';
        break;
    case 'society_admin': // Note: key in DB is society_admin
        include 'dashboards/society_head.php';
        break;
    case 'event_manager':
        include 'dashboards/event_manager.php';
        break;
    case 'finance_manager':
        include 'dashboards/finance_manager.php';
        break;
    case 'member':
    case 'student': // Legacy role support
        include 'dashboards/member.php';
        break;
    default:
        include 'dashboards/guest.php';
        break;
}

require_once 'includes/footer.php'; 
?>