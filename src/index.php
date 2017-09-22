<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
try {
    $db = new PDO('mysql:host=localhost;dbname=petits_comptes_entre_amis;charset=utf8', 'php', '3eXLjcN5PQXv39Vd');
} catch (Exception $e) {
    die($e->getMessage());
}

if (isset($_SESSION['username'])) {
    header('location:home.php');
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $record = $db->prepare('SELECT username, password FROM t_users WHERE username= :username');
    $record->bindParam(':username', $username);
    $record->execute();
    $result = $record->fetch(PDO::FETCH_ASSOC);

    if (count($result) > 0 && password_verify($password, $result['password'])) {
        $_SESSION['username'] = $username;
        header('location:home.php');
        exit;
    } else {
        echo '<br><div class="alert alert-danger" role="alert">';
        echo 'Username or password incorrect';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-5">
    <h1>Welcome to Petits Comptes Entre Amis</h1>
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name='username'>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name='password'>
        </div>
        <input type="submit" class="btn btn-primary" value="Login" name="login">
        <a href="signup.php" class="btn btn-secondary">Sign up</a>
    </form>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
        integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>