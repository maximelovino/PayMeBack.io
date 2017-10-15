<?php
session_start();
require_once "DBConnection.php";
require_once "DataValidator.php";
if (!isset($_SESSION)) {
	header('location:index.php');
}

if (isset($_POST['deleteDirectPayment'])) {
	$id = $_POST['id'];
	$payment = DBConnection::getInstance()->getDirectPayment($id);
	if (DataValidator::hasUserAccessToEvent($_SESSION['username'], $payment['event_id'])) {
		DBConnection::getInstance()->deleteDirectPaymentByID($id);
	}
	header('location: events.php?id=' . $payment['event_id']);
}
?>