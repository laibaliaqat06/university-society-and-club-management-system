<?php
require_once 'c:/xampp/htdocs/universal/core/db.php';
$stmt = $pdo->query('DESCRIBE clubs');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
$stmt = $pdo->query('SELECT * FROM sys_pages ORDER BY sort_order ASC');
print_r($stmt->fetchAll());
?>
