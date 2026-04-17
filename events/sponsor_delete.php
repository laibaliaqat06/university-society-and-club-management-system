<?php
require_once '../core/config.php';
require_once '../core/db.php';
session_start();

$role = $_SESSION['role'] ?? 'guest';
if (!in_array($role, ['super_admin', 'society_admin', 'event_manager'])) {
    die("Access Denied");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch the sponsor to check logo path
    $stmt = $pdo->prepare("SELECT logo_path FROM event_sponsors WHERE id = ?");
    $stmt->execute([$id]);
    $sponsor = $stmt->fetch();

    if ($sponsor) {
        // Delete logo file
        if (!empty($sponsor['logo_path']) && file_exists('../' . $sponsor['logo_path'])) {
            unlink('../' . $sponsor['logo_path']);
        }

        // Delete record
        $pdo->prepare("DELETE FROM event_sponsors WHERE id = ?")->execute([$id]);
    }
}

header("Location: sponsors.php");
exit;
