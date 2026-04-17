<?php
require_once 'c:/xampp/htdocs/universal/core/db.php';

try {
    // 1. Update sort_order for top-level menu items
    // Dashboard: 10
    // Societies: 20
    // Events: 30
    // Announcements: 40
    // Finance: 50
    // System Admin: 90
    // System Settings: 99

    $updates = [
        ['sort_order' => 10, 'page_name' => 'Dashboard'],
        ['sort_order' => 20, 'page_name' => 'Societies'],
        ['sort_order' => 30, 'page_name' => 'Events'],
        ['sort_order' => 40, 'page_name' => 'Announcements'],
        ['sort_order' => 50, 'page_name' => 'Finance'],
        ['sort_order' => 90, 'page_name' => 'System Admin'],
        ['sort_order' => 99, 'page_name' => 'System Settings'],
    ];

    $stmt = $pdo->prepare("UPDATE sys_pages SET sort_order = ? WHERE page_name = ? AND parent_id = 0");
    foreach ($updates as $u) {
        $stmt->execute([$u['sort_order'], $u['page_name']]);
    }

    // 2. Clear hardcoded items in sidebar.php or handle their duplication
    // Actually, I'll modify sidebar.php to remove the hardcoded ones.

    echo "Sidebar order updated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
