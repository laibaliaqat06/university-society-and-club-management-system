<?php
require_once 'core/db.php';
$stmt = $pdo->query("DESC events");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
