<?php
session_start();
require_once "DBConnection.php";
require_once "DataValidator.php";
if (!isset($_SESSION)) {
	header('location:index.php');
}

if (isset($_POST['deleteEvent'])) {
	if (DataValidator::hasUserAccessToEvent($_SESSION['username'], $_POST['id'])) {
		DBConnection::getInstance()->deleteEventByID($_POST['id']);
	}
	header('location:events.php');
}
?>