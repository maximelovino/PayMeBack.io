<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once "DataValidator.php";
require_once "DBConnection.php";

if (isset($_SESSION['username'])) {
	header('location:home.php');
}
$validUsername = true;
$correctLogin = true;
if (isset($_POST['login'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$validUsername = DataValidator::isValidUsername($username);
	if ($validUsername) {
		$login = DBConnection::getInstance()->loginOK($username, $password);
		if ($login) {
			$_SESSION['username'] = $username;
			header('location:home.php');
			exit;
		} else {
			$correctLogin = false;
		}
	} else {
		$correctLogin = false;
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
    <form action="" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name='username' required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name='password' required>
        </div>
        <input type="submit" class="btn btn-primary" value="Login" name="login">
        <a href="signup.php" class="btn btn-secondary">Sign up</a>
    </form>
	<?php
	if (!$correctLogin) {
		echo '<br><div class="alert alert-danger" role="alert">';
		echo 'Username or password incorrect';
		echo '</div>';
	}
	?>
</div>
</body>
<?php
include "footer.html";
?>
</html>