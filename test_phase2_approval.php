<?php
require_once 'core/db.php';
require_once 'core/Events.php';

$eventsObj = new Events($pdo);
$res = $eventsObj->updateFinanceStatus(131, 'approved', 7, 'Auto-approved for test');

echo "Update result: " . ($res ? "Success" : "Failure") . "\n";

$stmt = $pdo->prepare("SELECT admin_status, finance_status FROM events WHERE id = ?");
$stmt->execute([131]);
print_r($stmt->fetch());
?>
