<?php
$host = 'localhost';
$db   = 'universal_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$eventId = 148;
$images = [
    'uploads/events/highlights/highlights_collage.png',
    'uploads/events/highlights/faculty_interaction.png',
    'uploads/events/highlights/campus_students.png',
    'uploads/events/highlights/kabaddi_match.png',
    'uploads/events/highlights/campus_view.png'
];

foreach ($images as $img) {
    // Check if duplicate
    $stmt = $pdo->prepare("SELECT id FROM event_gallery WHERE event_id = ? AND image = ?");
    $stmt->execute([$eventId, $img]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO event_gallery (event_id, image, media_type) VALUES (?, ?, 'image')");
        $stmt->execute([$eventId, $img]);
        echo "Inserted: $img\n";
    } else {
        echo "Skipped (exists): $img\n";
    }
}
?>
