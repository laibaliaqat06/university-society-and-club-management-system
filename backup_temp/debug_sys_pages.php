<?php
require_once 'core/db.php';
$stmt = $pdo->prepare("SELECT * FROM sys_pages WHERE page_url LIKE ?");
$stmt->execute(['%approve_events%']);
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
