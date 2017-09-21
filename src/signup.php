<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
try {
    $db = new PDO('mysql:host=localhost;dbname=petits_comptes_entre_amis;charset=utf8', 'php', '3eXLjcN5PQXv39Vd');
} catch (Exception $e) {
    die($e->getMessage());
}
/*
if (isset($_SESSION['username'])){
    header('location:game.php');
}
*/
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"
          integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h2>Sign up for using this app</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name='username'>
        </div>
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" name='firstName'>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" id="lastName" name='lastName'>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name='email'>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name='password'>
        </div>
        <input type="submit" class="btn btn-primary" value="Sign Up" name="signup">
    </form>
    <?php

    if (isset($_POST['signup'])) {
        $username = trim($_POST['username']);
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if (!($username == '' || $password == '')) {
            $records = $db->prepare('SELECT username FROM t_users WHERE username= :uname');
            $records->bindParam(':uname', $username);
            $records->execute();
            $result = $records->fetchAll();
            if (count($result) > 0) {
                //username already taken
                echo '<br><br><div class="alert alert-danger" role="alert">';
                echo 'Username ' . $username . ' already taken';
                echo '</div>';
            } else {
                $insertion = $db->prepare('INSERT INTO t_users VALUES (:username,:first_name,:last_name,:email,:password)');
                $insertion->bindParam(':username', $username);
                $insertion->bindParam(':first_name', $firstName);
                $insertion->bindParam(':last_name', $lastName);
                $insertion->bindParam(':email', $email);
                $insertion->bindParam(':password', $hash);
                $insertion->execute();
                //TODO check answer?
                echo '<br><br><div class="alert alert-success" role="alert">';
                echo 'User ' . $username . ' added';
                echo '</div>';
            }
        }
    }
    ?>
</div>
</body>