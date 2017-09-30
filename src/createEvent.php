<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once "DBConnection.php";


if (!isset($_SESSION['username'])) {
	header("location: index.php");
}


if (isset($_POST['newEvent'])) {
	$title = trim($_POST['eventTitle']);
	$description = trim($_POST['eventDescription']);
	$users = $_POST['eventUsers'];
	array_push($users, $_SESSION['username']);
	$currency = trim($_POST['eventCurrency']);
	$weights = array();

	//TODO this is temporary, we should block "." in usernames...
	foreach ($users as $user) {
		$weights[$user] = $_POST["weight-" . str_replace(".", "_", $user)];
	}

	//TODO validation and trim

	DBConnection::getInstance()->insertNewEvent($title, $description, $users, $currency, $weights);
}

header('location:events.php');


?>