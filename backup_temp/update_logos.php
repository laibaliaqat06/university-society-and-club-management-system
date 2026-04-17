<?php
require_once 'core/db.php';

echo "Updating Clubs with details and images...\n";

// Dummy Images (Using plain colors/text via placeholder services or just generic URLs)
$banners = [
    'https://dummyimage.com/1200x400/0d6efd/ffffff&text=Debating+Society',
    'https://dummyimage.com/1200x400/20c997/ffffff&text=Coding+Club',
    'https://dummyimage.com/1200x400/ffc107/000000&text=Art+Club',
    'https://dummyimage.com/1200x400/dc3545/ffffff&text=Music+Society'
];

$logos = [
    'https://dummyimage.com/200x200/0d6efd/ffffff&text=DS',
    'https://dummyimage.com/200x200/20c997/ffffff&text=CC',
    'https://dummyimage.com/200x200/ffc107/000000&text=AC',
    'https://dummyimage.com/200x200/dc3545/ffffff&text=MS'
];

$gallery_images = [
    'https://dummyimage.com/600x400/adb5bd/ffffff&text=Event+Photo+1',
    'https://dummyimage.com/600x400/ced4da/ffffff&text=Event+Photo+2',
    'https://dummyimage.com/600x400/dee2e6/6c757d&text=Team+Meeting',
    'https://dummyimage.com/600x400/e9ecef/495057&text=Workshop'
];

$clubs = $pdo->query("SELECT id FROM clubs")->fetchAll();
$i = 0;

foreach ($clubs as $club) {
    $banner = $banners[$i % count($banners)];
    $logo = $logos[$i % count($logos)];
    
    // Update Main Info
    $stmt = $pdo->prepare("UPDATE clubs SET 
        cover_image = ?, 
        logo = ?, 
        contact_email = 'contact@club" . $club['id'] . ".com', 
        contact_phone = '+1234567890' 
        WHERE id = ?");
    $stmt->execute([$banner, $logo, $club['id']]);
    
    // Seed Gallery (3 images per club)
    for ($j = 0; $j < 3; $j++) {
        $img = $gallery_images[rand(0, 3)];
        $pdo->prepare("INSERT INTO club_gallery (club_id, image_url) VALUES (?, ?)")
            ->execute([$club['id'], $img]);
    }

    echo "Updated Club ID: " . $club['id'] . "\n";
    $i++;
}

echo "Clubs updated successfully.\n";
