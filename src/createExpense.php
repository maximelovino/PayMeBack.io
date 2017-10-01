<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once "DBConnection.php";

if (!isset($_SESSION['username'])) {
	header("location: index.php");
}


if (isset($_POST['newExpense'])) {
	//TODO validation
	$event_id = $_POST['event_id'];
	$title = $_POST['expenseTitle'];
	$description = $_POST['expenseDescription'];
	$date = $_POST['expenseDate'];
	$amount = floatval($_POST['expenseAmount']);
	$makerUsername = $_POST['expenseMaker'];
	$usersParticipating = array();
	//TODO round amount to correct rounding for currency
	$allUsers = DBConnection::getInstance()->selectUsersForEvent($event_id);

	foreach ($allUsers as $user) {
		if (isset($_POST['check-' . str_replace(".", "_", $user['username'])])) {
			array_push($usersParticipating, $user['username']);
		}
	}

	DBConnection::getInstance()->insertExpense($title, $description, $event_id, $amount, $date, $makerUsername, $usersParticipating);

}
header('location: events.php?id=' . $event_id);
?>