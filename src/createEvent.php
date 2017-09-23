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


if (isset($_POST['newEvent'])) {
    $title = $_POST['eventTitle'];
    $description = $_POST['eventDescription'];
    $currency = $_POST['eventCurrency'];
    $users = $_POST['eventUsers'];

    //TODO validation
    //TODO wrap in transaction and roll back if problem
    array_push($users,$_SESSION['username']);
    $query = $db->prepare('INSERT into t_groups VALUES (DEFAULT)');
    $query->execute();
    $idGroup = $db->lastInsertId();


    foreach ($users as $user) {
        $insertionQuery = $db->prepare('INSERT into t_group_membership VALUES (:username,:id)');
        $insertionQuery->bindParam(':username',$user);
        $insertionQuery->bindParam(':id',$idGroup);
        $insertionQuery->execute();
    }

    $eventInsertionSQL = 'INSERT into t_events VALUES (DEFAULT,:name,:desc,:group,:currency)';
    $eventInsertionQuery = $db->prepare($eventInsertionSQL);
    $eventInsertionQuery->bindParam(':name',$title);
    $eventInsertionQuery->bindParam(':desc',$description);
    $eventInsertionQuery->bindParam(':group',$idGroup);
    $eventInsertionQuery->bindParam(':currency',$currency);
    $eventInsertionQuery->execute();

}

header('location:events.php');


?>