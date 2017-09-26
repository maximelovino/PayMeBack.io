<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

try {
    $db = new PDO('mysql:host=localhost;dbname=petits_comptes_entre_amis;charset=utf8', 'php', '3eXLjcN5PQXv39Vd');
} catch (Exception $e) {
    die($e->getMessage());
}
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