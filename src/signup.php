<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
if (isset($_SESSION['username'])) {
	header('location:home.php');
}
require_once "DBConnection.php";
require_once "DataValidator.php";
$validUsername = true;
$validFirstName = true;
$validLastName = true;
$validEmail = true;

if (isset($_POST['signup'])) {
	$username = trim($_POST['username']);
	$firstName = trim($_POST['firstName']);
	$lastName = trim($_POST['lastName']);
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	$validUsername = DataValidator::isValidUsername($username);
	$validFirstName = DataValidator::isValidName($firstName);
	$validLastName = DataValidator::isValidName($lastName);
	$validEmail = DataValidator::isValidEmail($email);

	if ($validEmail && $validLastName && $validFirstName && $validUsername) {
		$hash = password_hash($password, PASSWORD_DEFAULT);

		if (!($password == '')) {
			$result = DBConnection::getInstance()->getSingleUser($username);
			if ($result != null) {
				//username already taken
				echo '<br><div class="alert alert-danger" role="alert">';
				echo 'Username ' . $username . ' already taken';
				echo '</div>';
			} else {
				if (DBConnection::getInstance()->insertNewUser($username, $firstName, $lastName, $email, $hash)) {
					header('location:index.php');
				} else {
					echo '<br><div class="alert alert-danger" role="alert">';
					echo 'There was an error signing you up';
					echo '</div>';
				}
			}
		}
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
    <link rel="stylesheet" href="css/custom.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"
            integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
            integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
            crossorigin="anonymous"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<?php
include "navbarNoLinks.html";
?>
<div class="container mt-5">
    <h2>Sign up for using this app</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username</label>
			<?php
			$classUsername = 'form-control';
			if (!$validUsername) {
				$classUsername .= ' is-invalid';
			}
			?>
            <input type="text" class="<?php echo $classUsername ?>" id="username" name='username' required>
            <small class="form-text text-muted">The username must contain only letters from a-z and numbers from 0-9 and
                be smaller than 256 characters.
            </small>
        </div>
        <div class="form-group">
			<?php
			$classFirstName = 'form-control';
			if (!$validFirstName) {
				$classFirstName .= ' is-invalid';
			}
			?>
            <label for="firstName">First Name</label>
            <input type="text" class="<?php echo $classFirstName ?>" id="firstName" name='firstName' required>
        </div>
        <div class="form-group">
			<?php
			$classLastName = 'form-control';
			if (!$validLastName) {
				$classLastName .= ' is-invalid';
			}
			?>
            <label for="lastName">Last Name</label>
            <input type="text" class="<?php echo $classLastName ?>" id="lastName" name='lastName' required>
        </div>
        <div class="form-group">
			<?php
			$classEmail = 'form-control';
			if (!$validEmail) {
				$classEmail .= ' is-invalid';
			}
			?>
            <label for="email">Email</label>
            <input type="email" class="<?php echo $classEmail ?>" id="email" name='email' required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name='password' required>
        </div>
        <input type="submit" class="btn btn-primary" value="Sign Up" name="signup">
    </form>
</div>
<?php
include "footer.html";
?>
</body>