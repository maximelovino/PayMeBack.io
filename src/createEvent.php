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
	$users = trim($_POST['eventUsers']);
	array_push($users, $_SESSION['username']);
	$currency = trim($_POST['eventCurrency']);
	//TODO validation and trim

	DBConnection::getInstance()->insertNewEvent($title, $description, $users, $currency);
}

header('location:events.php');


?>