<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'DBConnection.php';
require_once 'DataValidator.php';

if (!isset($_SESSION['username'])) {
	header("location: index.php");
}

if (isset($_GET['id'])) {
	$id = $_GET['id'];
	if (DBConnection::getInstance()->selectSingleEventByID($id) == null) {
		header('location: error_pages/404.php');
	}
}

$validTitle = true;
$validCurrency = true;
$validWeights = array();
$validUsers = array();
$validUserArray = true;
$showModal = false;

if (isset($_POST['newEvent'])) {
	$eventTitle = trim($_POST['eventTitle']);
	$validTitle = DataValidator::isValidTitle($eventTitle);
	$description = trim($_POST['eventDescription']);
	$users = $_POST['eventUsers'];
	array_push($users, $_SESSION['username']);
	$currency = trim($_POST['eventCurrency']);
	$validCurrency = DataValidator::isValidCurrency($currency);
	$weights = array();
	//TODO trim all users and weights
	foreach ($users as $user) {
		$validUsers[$user] = DataValidator::usernameExists($user);
		$weights[$user] = $_POST["weight-" . $user];
		$validWeights[$user] = DataValidator::isValidWeight($weights[$user]);
	}
	$validUserArray = DataValidator::isValidUsersArray($users);

	if ($validUserArray && $validTitle && $validCurrency && !in_array(false, $validWeights, true) && !in_array(false, $validUsers, true)) {
		DBConnection::getInstance()->insertNewEvent($eventTitle, $description, $users, $currency, $weights);
	} else {
		$showModal = true;
	}
}

$validReimbursementPayer = true;
$validReimbursementPayed = true;
$validReimbursementAmount = true;
$validReimbursementDate = true;
$usersDifferent = true;

if (isset($_POST['newReimbursement'])) {
	$payingUser = trim($_POST['paying_user']);
	$payedUser = trim($_POST['payed_user']);
	$validReimbursementPayer = DataValidator::usernameExists($payingUser);
	$validReimbursementPayed = DataValidator::usernameExists($payedUser);
	$reimbursementEventID = trim($_POST['event_id']);
	$amount = trim($_POST['amount']);
	$validReimbursementAmount = DataValidator::isValidAmount($amount);
	$date = trim($_POST['date']);
	$validReimbursementDate = DataValidator::isValidDate($date);

	$usersDifferent = $payingUser != $payedUser;

	if ($validReimbursementDate && $validReimbursementPayer && $validReimbursementPayed && $validReimbursementAmount && $usersDifferent) {
		DBConnection::getInstance()->insertReimbursement($payingUser, $payedUser, $reimbursementEventID, $amount, $date);
	}
	header('location: events.php?id=' . $reimbursementEventID);
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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
</head>

<body>
<div class="container mt-5">
	<?php
	include 'navbar.html';
	if (isset($_GET['id'])) {
		include 'singleEventPageBody.php';
	} else {
		include 'allEventsBody.php';
	}
	?>

</div>
<script type="text/javascript">
    $("#eventsLink").toggleClass("btn-outline-dark");
    $("#eventsLink").toggleClass("btn-dark");
</script>
</body>

