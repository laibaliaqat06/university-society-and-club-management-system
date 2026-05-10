<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Get the current script name (e.g., 'login.php')
$current_page = basename($_SERVER['PHP_SELF']);

// List of pages that DO NOT require login
$public_pages = ['login.php', 'register.php', 'index.php'];

// Auth Check Logic
if (!isset($_SESSION['user_id'])) {
    // If user is NOT logged in AND trying to access a protected page
    if (!in_array($current_page, $public_pages)) {
        header("Location: " . BASE_URL . "modules/login.php");
        exit;
    }
    // Set role to guest if not set (for public pages)
    if (!isset($_SESSION['role'])) {
        $_SESSION['role'] = 'guest';
    }
} 
// Conflict Fix: If user IS logged in but tries to go to Login/Register, send them to Dashboard
else {
    if (in_array($current_page, ['login.php', 'register.php'])) {
        header("Location: " . BASE_URL . "index.php");
        exit;
    }
}

// Helper to determine color based on role string
function getRoleBadgeColor($roleName) {
    $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
    $index = crc32($roleName) % count($colors);
    return 'text-bg-' . $colors[$index];
}
?>