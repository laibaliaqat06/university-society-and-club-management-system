<?php
require_once __DIR__ . '/core/db.php';

try {
    // 1. Add volunteers_needed to events
    $stmt = $pdo->prepare("SHOW COLUMNS FROM events LIKE 'volunteers_needed'");
    $stmt->execute();
    if($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE events ADD COLUMN volunteers_needed INT DEFAULT 0");
        echo "Added 'volunteers_needed' column to 'events'.\n";
    }

    // 2. Create event_sponsors
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS event_sponsors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            sponsor_name VARCHAR(255) NOT NULL,
            contribution_amount DECIMAL(10,2) DEFAULT 0,
            logo_path VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
        )
    ");
    echo "Created 'event_sponsors' table.\n";

    // 3. Create event_volunteer_apps
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS event_volunteer_apps (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            status ENUM('pending', 'selected', 'rejected') DEFAULT 'pending',
            applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_app (event_id, user_id)
        )
    ");
    echo "Created 'event_volunteer_apps' table.\n";

    // 4. Update sys_pages
    $stmt = $pdo->prepare("SELECT id FROM sys_pages WHERE page_name = ?");
    
    $stmt->execute(['Sponsors']);
    $sponsorPageId = $stmt->fetchColumn();
    if (!$sponsorPageId) {
        $pdo->prepare("INSERT INTO sys_pages (parent_id, page_name, page_url, icon_class, sort_order) VALUES (?, ?, ?, ?, ?)")
            ->execute([8, 'Sponsors', 'events/sponsors.php', 'bi bi-circle', 3]);
        $sponsorPageId = $pdo->lastInsertId();
    }

    $stmt->execute(['Volunteers']);
    $volunteerPageId = $stmt->fetchColumn();
    if (!$volunteerPageId) {
        $pdo->prepare("INSERT INTO sys_pages (parent_id, page_name, page_url, icon_class, sort_order) VALUES (?, ?, ?, ?, ?)")
            ->execute([8, 'Volunteers', 'events/volunteers.php', 'bi bi-circle', 4]);
        $volunteerPageId = $pdo->lastInsertId();
    }

    // 5. Assign Permissions
    $stmt = $pdo->prepare("INSERT IGNORE INTO role_access (role_key, page_id) VALUES (?, ?)");
    
    // Admin roles access both pages
    $adminRoles = ['super_admin', 'society_admin', 'event_manager'];
    foreach ($adminRoles as $r) {
        $stmt->execute([$r, $sponsorPageId]);
        $stmt->execute([$r, $volunteerPageId]);
    }

    // Member roles access volunteers page
    $stmt->execute(['member', $volunteerPageId]);
    $stmt->execute(['student', $volunteerPageId]);

    echo "Added pages to navigation and configured permissions.\n";
    echo "Migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
