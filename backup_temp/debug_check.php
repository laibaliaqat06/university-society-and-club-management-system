<?php
require_once 'core/db.php';

echo "--- Debugging Database State ---\n";

try {
    // Check Roles
    echo "\n[sys_roles]\n";
    $stmt = $pdo->query("SELECT * FROM sys_roles");
    $roles = $stmt->fetchAll();
    if (empty($roles)) {
        echo "EMPTY! Table exists but no data.\n";
    } else {
        foreach ($roles as $r) {
            echo "- {$r['role_key']} ({$r['role_name']})\n";
        }
    }

    // Check Pages
    echo "\n[sys_pages]\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM sys_pages");
    echo "Count: " . $stmt->fetchColumn() . "\n";

    // Check Access
    echo "\n[role_access]\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM role_access");
    echo "Count: " . $stmt->fetchColumn() . "\n";

    // Check Users
    echo "\n[users]\n";
    $stmt = $pdo->query("SELECT id, name, email, role FROM users LIMIT 5");
    $users = $stmt->fetchAll();
    foreach ($users as $u) {
        echo "- ID: {$u['id']}, Role: {$u['role']}, Email: {$u['email']}\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
