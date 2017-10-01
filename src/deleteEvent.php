<?php
session_start();
require_once "DBConnection.php";
if (!isset($_SESSION)) {
	header('location:index.php');
}

if (isset($_POST['deleteEvent'])) {
	//TODO you must be able to delete only if it's one of your events
	$eventsForUser = DBConnection::getInstance()->getAllEventsForUser($_SESSION['username']);
	$isValid = false;
	foreach ($eventsForUser as $event) {
		if ($event['event_id'] == $_POST['id']) {
			$isValid = true;
			break;
		}
	}

	if ($isValid) {
		DBConnection::getInstance()->deleteEventByID($_POST['id']);
		header('location:events.php');
	} else {
		//TODO return 404
	}
}
?>