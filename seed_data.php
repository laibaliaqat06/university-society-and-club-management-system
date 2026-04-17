<?php
require_once 'core/db.php';

echo "Seeding data...\n";

// 1. Seed Users
$users = [
    ['Super Admin', 'admin@universal.com', '123456', 'super_admin'],
    ['Society Head', 'head@society.com', '123456', 'society_admin'],
    ['Event Manager', 'event@manager.com', '123456', 'event_manager'],
    ['Finance Manager', 'finance@manager.com', '123456', 'finance_manager'],
    ['John Doe', 'john@student.com', '123456', 'member'],
    ['Jane Smith', 'jane@student.com', '123456', 'member'],
    ['Alice Johnson', 'alice@student.com', '123456', 'member'],
    ['Bob Brown', 'bob@student.com', '123456', 'member'],
    ['Charlie Davis', 'charlie@student.com', '123456', 'member'],
    ['Guest User', 'guest@user.com', '123456', 'guest']
];

$userIds = [];
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
foreach ($users as $u) {
    try {
        // Check if user exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$u[1]]);
        if ($id = $check->fetchColumn()) {
             $userIds[$u[3]][] = $id; // Group by role
             echo "User {$u[0]} exists (ID: $id).\n";
             continue;
        }

        $stmt->execute([$u[0], $u[1], password_hash($u[2], PASSWORD_DEFAULT), $u[3]]);
        $id = $pdo->lastInsertId();
        $userIds[$u[3]][] = $id;
        echo "Created user: {$u[0]} (ID: $id)\n";
    } catch (PDOException $e) {
        echo "Error creating user {$u[0]}: " . $e->getMessage() . "\n";
    }
}

// 2. Seed Clubs
$clubs = [
    ['Debating Society', 'For those who love to argue.', 'society_admin', 'https://images.unsplash.com/photo-1544928147-79a2dbc1f389?auto=format&fit=crop&q=80&w=800', 'debate@uni.com', '+123456789', 'https://images.unsplash.com/photo-1471439274527-a5169856ad96?auto=format&fit=crop&q=80&w=1200', 'Academic'],
    ['Coding Club', 'For the tech enthusiasts.', 'super_admin', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=800', 'code@uni.com', '+123456780', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=1200', 'Tech'],
    ['Art Club', 'Express yourself.', 'society_admin', 'https://images.unsplash.com/photo-1460518451285-97b6aa32095a?auto=format&fit=crop&q=80&w=800', 'art@uni.com', '+123456781', 'https://images.unsplash.com/photo-1456086272160-b28b0645b729?auto=format&fit=crop&q=80&w=1200', 'Arts'],
    ['Music Society', 'Feel the rhythm.', 'society_admin', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&q=80&w=800', 'music@uni.com', '+123456782', 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?auto=format&fit=crop&q=80&w=1200', 'Arts'],
    ['IT & Innovation Club', 'A university based student innovation hub.', 'society_admin', 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800', 'it@uni.com', '+123456783', 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=1200', 'Tech'],
    ['Test Club', 'A club for testing purposes.', 'super_admin', 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=800', 'test@uni.com', '+123456784', 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&q=80&w=1200', 'Tech'],
    ['Science Club', 'Explore the wonders of science through experiments and events.', 'society_admin', 'https://images.unsplash.com/photo-1507413245164-6160d8298b31?auto=format&fit=crop&q=80&w=800', 'science@uni.com', '+123456785', 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&q=80&w=1200', 'Academic'],
    ['Literature Society', 'For the love of books, poetry, and prose.', 'society_admin', 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&q=80&w=800', 'literature@uni.com', '+123456786', 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&q=80&w=1200', 'Academic'],
    ['Theater Group', 'Acting, writing, and stage production.', 'society_admin', 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?auto=format&fit=crop&q=80&w=800', 'theater@uni.com', '+123456787', 'https://images.unsplash.com/photo-1491321415170-c75cbedbf0fb?auto=format&fit=crop&q=80&w=1200', 'Arts'],
    ['Football Club', 'Training, matches, and tournaments.', 'society_admin', 'https://images.unsplash.com/photo-1518605368461-1e967a5b3a4d?auto=format&fit=crop&q=80&w=800', 'football@uni.com', '+123456788', 'https://images.unsplash.com/photo-1511886929837-354d827aae26?auto=format&fit=crop&q=80&w=1200', 'Sports'],
    ['Basketball Team', 'Dribble, shoot, and score.', 'society_admin', 'https://images.unsplash.com/photo-1519861531473-9200262188bf?auto=format&fit=crop&q=80&w=800', 'basketball@uni.com', '+123456789', 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&q=80&w=1200', 'Sports'],
    ['Robotics Club', 'Build and program robots from scratch.', 'super_admin', 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800', 'robotics@uni.com', '+123456790', 'https://images.unsplash.com/photo-1535378620166-273708d44e4c?auto=format&fit=crop&q=80&w=1200', 'Tech'],
    ['Volunteers Society', 'Give back to the community and help those in need.', 'society_admin', 'https://images.unsplash.com/photo-1593113580326-9e67ca2b6534?auto=format&fit=crop&q=80&w=800', 'volunteer@uni.com', '+123456791', 'https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&q=80&w=1200', 'Social'],
    ['Green Environment Club', 'Promoting sustainability and ecological awareness.', 'society_admin', 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=800', 'green@uni.com', '+123456792', 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?auto=format&fit=crop&q=80&w=1200', 'Social']
];

$clubIds = [];
$stmt = $pdo->prepare("INSERT INTO clubs (name, description, created_by, logo, contact_email, contact_phone, cover_image, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($clubs as $c) {
    try {
        $creatorRole = $c[2]; 
        $creatorId = $userIds[$creatorRole][0] ?? $userIds['super_admin'][0];

        // Check if club exists
        $check = $pdo->prepare("SELECT id FROM clubs WHERE name = ?");
        $check->execute([$c[0]]);
        $existingId = $check->fetchColumn();
        
        if ($existingId) {
            // Update details for existing club
            $pdo->prepare("UPDATE clubs SET logo = ?, contact_email = ?, contact_phone = ?, cover_image = ?, category = ? WHERE id = ?")
                ->execute([$c[3], $c[4], $c[5], $c[6], $c[7], $existingId]);
            $clubIds[] = $existingId;
            echo "Updated club: {$c[0]} (ID: $existingId).\n";
            continue;
        }

        $stmt->execute([$c[0], $c[1], $creatorId, $c[3], $c[4], $c[5], $c[6], $c[7]]);
        $id = $pdo->lastInsertId();
        $clubIds[] = $id;
        echo "Created club: {$c[0]} (ID: $id)\n";
    } catch (PDOException $e) {
         echo "Club {$c[0]} error: " . $e->getMessage() . "\n";
    }
}

// 2.5 Seed Gallery
echo "Seeding gallery...\n";
$galleryImages = [
    'https://images.unsplash.com/photo-1523240715630-9917c18cc850?auto=format&fit=crop&q=80&w=800',
    'https://images.unsplash.com/photo-1523580494863-6f30312245d5?auto=format&fit=crop&q=80&w=800',
    'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=800'
];

$stmt = $pdo->prepare("INSERT INTO club_gallery (club_id, image_url) VALUES (?, ?)");
foreach ($clubIds as $cid) {
    foreach ($galleryImages as $img) {
        try {
            $stmt->execute([$cid, $img]);
        } catch (Exception $e) {}
    }
}

// 3. Seed Club Memberships
echo "Seeding memberships...\n";
$memberIds = $userIds['member'] ?? [];
if (!empty($clubIds) && !empty($memberIds)) {
    // Clear old memberships for clean seeding if needed, or just insert
    $stmt = $pdo->prepare("INSERT INTO club_memberships (user_id, club_id, role) VALUES (?, ?, ?)");
    foreach ($clubIds as $cid) {
        foreach ($memberIds as $uid) {
             // Randomly assign members to clubs
             if (rand(0, 1)) {
                 try {
                     $stmt->execute([$uid, $cid, 'member']);
                 } catch (Exception $e) { /* Ignore dupes */ }
             }
        }
    }
}

// 4. Seed Events
$events = [
    ['Annual Debate', 'The big debate event.', '+1 month', 'Auditorium'],
    ['Hackathon 2024', 'Code all night.', '+2 weeks', 'Lab 1'],
    ['Art Exhibition', 'Showcase your work.', '-1 week', 'Hall A'],
    ['Music Concert', 'Live music.', '+5 days', 'Stadium']
];

$stmt = $pdo->prepare("INSERT INTO events (club_id, title, description, event_date, location, created_by) VALUES (?, ?, ?, ?, ?, ?)");
if (empty($clubIds)) {
    echo "No clubs found to assign events to. Skipping event seeding.\n";
} else {
    foreach ($events as $k => $e) {
        try {
            $cid = $clubIds[$k % count($clubIds)];
            $creator = $userIds['event_manager'][0] ?? $userIds['super_admin'][0];
            $date = date('Y-m-d H:i:s', strtotime($e[2]));
            
            $stmt->execute([$cid, $e[0], $e[1], $date, $e[3], $creator]);
            echo "Created event: {$e[0]}\n";
        } catch (PDOException $ex) {
            echo "Event error: " . $ex->getMessage() . "\n";
        }
    }
}

// 5. Seed Finance Records
echo "Seeding finance records...\n";
$financeRecords = [
    [1000.00, 'income', 'Sponsorship from TechCorp', 'approved'],
    [500.00, 'expense', 'Catering for Annual Debate', 'pending'],
    [200.00, 'expense', 'Printing flyers', 'approved'],
    [150.00, 'income', 'Membership fees', 'approved']
];

$stmt = $pdo->prepare("INSERT INTO finance_records (club_id, amount, type, description, status, created_by) VALUES (?, ?, ?, ?, ?, ?)");
if (!empty($clubIds)) {
    foreach ($financeRecords as $fr) {
        $cid = $clubIds[array_rand($clubIds)];
        $creator = $userIds['society_admin'][0] ?? $userIds['super_admin'][0];
        try {
            $stmt->execute([$cid, $fr[0], $fr[1], $fr[2], $fr[3], $creator]);
        } catch (Exception $e) {
            echo "Finance seed error: " . $e->getMessage() . "\n"; 
        }
    }
}

echo "Seeding completed.\n";
