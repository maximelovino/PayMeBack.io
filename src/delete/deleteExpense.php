<?php
session_start();
require_once "../DBConnection.php";
require_once "../DataValidator.php";
if (!isset($_SESSION)) {
	header('location:index.php');
}

if (isset($_POST['deleteExpense'])) {
	$id = $_POST['id'];
	$expense = DBConnection::getInstance()->getSingleExpenseDetail($id);
	if (DataValidator::hasUserAccessToEvent($_SESSION['username'], $expense['event_id'])) {
		DBConnection::getInstance()->deleteExpenseByID($id);
	}
	header('location: /events.php?id=' . $expense['event_id']);
}
?>