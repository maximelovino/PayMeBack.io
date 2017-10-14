<?php
session_start();
require_once "DBConnection.php";
if (!isset($_SESSION)) {
	header('location:index.php');
}

if (isset($_POST['deleteExpense'])) {
	$id = $_POST['id'];
	$events = DBConnection::getInstance()->getAllEventsForUser($_SESSION['username']);
	$expense = DBConnection::getInstance()->getSingleExpenseDetail($id);
	$validRequest = false;
	foreach ($events as $event) {
		if ($event['event_id'] == $expense['event_id']) {
			$validRequest = true;
			break;
		}
	}
	if ($validRequest) {
		DBConnection::getInstance()->deleteExpenseByID($id);
	}
	header('location: events.php?id=' . $expense['event_id']);
}
?>