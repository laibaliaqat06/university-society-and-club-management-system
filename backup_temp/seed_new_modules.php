<?php
require_once __DIR__ . '/core/db.php';

try {
    // 1. Get an active event. Let's get the latest two events.
    $stmt = $pdo->query("SELECT id FROM events ORDER BY id DESC LIMIT 2");
    $events = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($events) < 1) {
        die("No events found to seed data into.");
    }

    $event1 = $events[0];
    $event2 = $events[1] ?? $events[0];

    // 2. Set volunteers needed
    $pdo->prepare("UPDATE events SET volunteers_needed = 10 WHERE id = ?")->execute([$event1]);
    $pdo->prepare("UPDATE events SET volunteers_needed = 5 WHERE id = ?")->execute([$event2]);

    echo "Updated event IDs ($event1, $event2) to require volunteers.\n";

    // 3. Add Sponsors
    $pdo->exec("DELETE FROM event_sponsors"); // Clear existing for clean slate
    $sponsors = [
        [$event1, 'TechCorp Inc.', 5000.00, ''],
        [$event1, 'Global Media Group', 1500.00, ''],
        [$event2, 'Local Eats', 250.00, '']
    ];

    $stmt = $pdo->prepare("INSERT INTO event_sponsors (event_id, sponsor_name, contribution_amount, logo_path) VALUES (?, ?, ?, ?)");
    foreach ($sponsors as $s) {
        $stmt->execute($s);
    }
    echo "Added 3 sample sponsors.\n";

    // 4. Get some user IDs to act as volunteers (members/students)
    $stmt = $pdo->query("SELECT id FROM users WHERE role IN ('member', 'student') LIMIT 4");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($users) > 0) {
        $pdo->exec("DELETE FROM event_volunteer_apps"); // Clear existing
        $stmt = $pdo->prepare("INSERT INTO event_volunteer_apps (event_id, user_id, status) VALUES (?, ?, ?)");
        
        if (isset($users[0])) {
            $stmt->execute([$event1, $users[0], 'selected']);
        }
        if (isset($users[1])) {
            $stmt->execute([$event1, $users[1], 'pending']);
        }
        if (isset($users[2])) {
            $stmt->execute([$event1, $users[2], 'rejected']);
        }
        if (isset($users[3])) {
            $stmt->execute([$event2, $users[3], 'selected']);
        }
        echo "Added sample volunteer applications with various statuses.\n";
    }

    echo "Sample data successfully seeded! You can now view the flow in the app.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
