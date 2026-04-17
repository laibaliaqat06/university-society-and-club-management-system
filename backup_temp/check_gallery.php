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

$stmt = $pdo->prepare("SELECT * FROM event_gallery WHERE event_id = 148");
$stmt->execute();
$gallery = $stmt->fetchAll();

echo "GALLERY ITEMS FOR EVENT 148:\n";
foreach ($gallery as $item) {
    echo "ID: " . $item['id'] . " | Image: " . $item['image'] . "\n";
}
?>
