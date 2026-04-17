<?php
require_once 'includes/header.php';
require_once 'core/Events.php';

$eventsObj = new Events($pdo);
$pastEvents = $eventsObj->getPastEvents();

echo "PAST EVENTS:\n";
foreach ($pastEvents as $event) {
    echo "ID: " . $event['id'] . " | Title: " . $event['title'] . " | Date: " . $event['event_date'] . "\n";
}
?>
