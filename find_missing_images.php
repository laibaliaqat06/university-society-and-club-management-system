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

$stmt = $pdo->prepare("SELECT image FROM event_gallery");
$stmt->execute();
$galleryImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

$dir = 'uploads/events/highlights/';
$files = scandir($dir);

echo "FILES IN DIRECTORY BUT NOT IN GALLERY:\n";
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    $path = $dir . $file;
    if (!in_array($path, $galleryImages)) {
        echo $path . "\n";
    }
}
?>
