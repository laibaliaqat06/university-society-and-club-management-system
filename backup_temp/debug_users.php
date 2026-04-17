<?php
require_once 'core/db.php';
$stmt = $pdo->query("SELECT id, name, role FROM users");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
