<?php
require_once 'core/config.php';
require_once 'core/db.php';

try {
    $sql = "
    -- Create Users Table
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'member',
        avatar VARCHAR(255) DEFAULT NULL,
        identity_no VARCHAR(50),
        registration_no VARCHAR(50),
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Create Clubs Table (Societies)
    CREATE TABLE IF NOT EXISTS clubs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT 'General',
        description TEXT,
        mission TEXT,
        vision TEXT,
        logo VARCHAR(255),
        cover_image VARCHAR(255),
        contact_email VARCHAR(100),
        contact_phone VARCHAR(50),
        social_links JSON,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    );

    -- Create Club Gallery Table
    CREATE TABLE IF NOT EXISTS club_gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        club_id INT,
        image_url VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE
    );

    -- Create Club Memberships Table
    CREATE TABLE IF NOT EXISTS club_memberships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        club_id INT,
        role ENUM('member', 'admin', 'president', 'staff', 'coordinator') DEFAULT 'member',
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE
    );

    -- Create Events Table
    CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        club_id INT,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        event_date DATETIME,
        location VARCHAR(255),
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    );

    -- Create Event RSVPs Table
    CREATE TABLE IF NOT EXISTS event_rsvps (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT,
        user_id INT,
        status ENUM('going', 'maybe', 'not_going') DEFAULT 'going',
        rsvp_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    -- 1. System Roles
    CREATE TABLE IF NOT EXISTS sys_roles (
        role_key VARCHAR(50) PRIMARY KEY,
        role_name VARCHAR(100) NOT NULL,
        is_system_role BOOLEAN DEFAULT 0
    );

    -- 2. System Pages (Menu Structure)
    CREATE TABLE IF NOT EXISTS sys_pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        parent_id INT DEFAULT 0,
        page_name VARCHAR(100) NOT NULL,
        page_url VARCHAR(255) NOT NULL, -- '#' for parent menu items
        icon_class VARCHAR(50),
        sort_order INT DEFAULT 0
    );

    -- 3. Role Access (Permissions)
    CREATE TABLE IF NOT EXISTS role_access (
        role_key VARCHAR(50),
        page_id INT,
        PRIMARY KEY (role_key, page_id),
        FOREIGN KEY (role_key) REFERENCES sys_roles(role_key) ON DELETE CASCADE,
        FOREIGN KEY (page_id) REFERENCES sys_pages(id) ON DELETE CASCADE
    );

    -- Create Finance Records Table
    CREATE TABLE IF NOT EXISTS finance_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        club_id INT,
        amount DECIMAL(10, 2) NOT NULL,
        type ENUM('income', 'expense') NOT NULL,
        description VARCHAR(255) NOT NULL,
        record_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_by INT,
        FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    );
    ";

    $pdo->exec($sql);

    // --- SEED DATA ---

    // 1. Seed Roles
    $roles = [
        ['super_admin', 'Super Admin', 1],
        ['society_admin', 'Society Head', 0],
        ['event_manager', 'Event Manager', 0],
        ['finance_manager', 'Finance Manager', 0],
        ['member', 'Member/Student', 0],
        ['guest', 'Guest', 1]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO sys_roles (role_key, role_name, is_system_role) VALUES (?, ?, ?)");
    foreach ($roles as $role) {
        $stmt->execute($role);
    }

    // 2. Seed Pages
    // FORCE RESET for this update to ensure all menu items appear
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE sys_pages; TRUNCATE TABLE role_access; SET FOREIGN_KEY_CHECKS=1;");
    
    $pages = [
        // [id, parent_id, name, url, icon, order]
        [1, 0, 'Dashboard', 'index.php', 'bi bi-speedometer', 10],
        [2, 0, 'User Management', '#', 'bi bi-people', 20],
        [3, 2, 'All Users', 'users/index.php', 'bi bi-circle', 1],
        [4, 2, 'Add User', 'users/create.php', 'bi bi-circle', 2],
        [5, 0, 'Societies', '#', 'bi bi-collection', 30],
        [6, 5, 'All Societies', 'clubs/index.php', 'bi bi-circle', 1],
        [7, 5, 'My Society', 'clubs/mysociety.php', 'bi bi-circle', 2],
        [8, 0, 'Events', '#', 'bi bi-calendar-event', 40],
        [9, 8, 'All Events', 'events/index.php', 'bi bi-circle', 1],
        [10, 8, 'Manage Events', 'events/manage.php', 'bi bi-circle', 2],
        [11, 0, 'Finance', '#', 'bi bi-cash-coin', 50],
        [12, 11, 'Budget Overview', 'finance/overview.php', 'bi bi-circle', 1],
        [13, 0, 'System Settings', 'settings.php', 'bi bi-gear', 99],
        [14, 0, 'System Admin', '#', 'bi bi-shield-lock', 90],
        [15, 14, 'Manage Users', 'dashboards/super_admin/manage_users.php', 'bi bi-circle', 1],
        [16, 14, 'Manage Roles', 'dashboards/super_admin/manage_roles.php', 'bi bi-circle', 2],
        [17, 14, 'Manage Pages', 'dashboards/super_admin/manage_pages.php', 'bi bi-circle', 3]
    ];

    $stmt = $pdo->prepare("INSERT INTO sys_pages (id, parent_id, page_name, page_url, icon_class, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($pages as $p) {
        $stmt->execute($p);
    }

    // 3. Map Default Permissions
    // Helper to insert access
    $accessStmt = $pdo->prepare("INSERT IGNORE INTO role_access (role_key, page_id) VALUES (?, ?)");
    
    $permissions = [
        'super_admin' => [1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17], // Everything
        'society_admin' => [1, 5, 6, 7, 8, 9, 10, 11, 12], // Club management + Events + Finance
        'event_manager' => [1, 8, 9, 10], // Events only
        'finance_manager' => [1, 11, 12], // Finance only
        'member' => [1, 5, 6, 8, 9], // View clubs and events
        'student' => [1, 5, 6, 8, 9], // Legacy student role: Same as member
        'guest' => [] // Guest usually has no backend access, or limited
    ];

    foreach ($permissions as $role => $pageIds) {
        foreach ($pageIds as $pid) {
            $accessStmt->execute([$role, $pid]);
        }
    }
    echo "Database migration completed successfully.\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
?>
