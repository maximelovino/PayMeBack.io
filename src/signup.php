<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once "DBConnection.php";
require_once "DataValidator.php"
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
<div class="container mt-5">
    <h2>Sign up for using this app</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name='username' required>
        </div>
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" name='firstName' required>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" id="lastName" name='lastName' required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name='email' required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name='password' required>
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

		if (DataValidator::isValidUsername($username) && DataValidator::isValidName($firstName) && DataValidator::isValidName($lastName) && DataValidator::isValidEmail($email)) {
			$hash = password_hash($password, PASSWORD_DEFAULT);

			if (!($username == '' || $password == '')) {
				$result = DBConnection::getInstance()->getUsersMatching($username);
				if (count($result) > 0) {
					//username already taken
					echo '<br><div class="alert alert-danger" role="alert">';
					echo 'Username ' . $username . ' already taken';
					echo '</div>';
				} else {
					DBConnection::getInstance()->insertNewUser($username, $firstName, $lastName, $email, $hash);
					//TODO check answer?
					echo '<br><div class="alert alert-success" role="alert">';
					echo 'User ' . $username . ' added';
					echo '</div>';
					echo '<a href="index.php" class="btn btn-primary">Proceed to login</a>';
				}
			}
		} else {
			http_response_code(400);
		}
	}
	?>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
        integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
</body>