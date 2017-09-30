<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once "DBConnection.php";

if (!isset($_SESSION['username'])) {
    header("location: index.php");
}


if (isset($_POST['newExpense'])){
    //TODO validation
    $title = $_POST['expenseTitle'];
    $description = $_POST['expenseDescription'];
    $date = $_POST['expenseDate'];
    $amount = floatval($_POST['expenseAmount']);
    $makerUsername = $_POST['expenseMaker'];
}
?>