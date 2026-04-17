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

$stmt = $pdo->query("SELECT id, title, event_date FROM events WHERE admin_status = 'approved' AND finance_status = 'approved' AND event_date < CURDATE() ORDER BY event_date DESC");
$events = $stmt->fetchAll();

echo "PAST EVENTS:\n";
foreach ($events as $event) {
    echo "ID: " . $event['id'] . " | Title: " . $event['title'] . " | Date: " . $event['event_date'] . "\n";
}
?>
