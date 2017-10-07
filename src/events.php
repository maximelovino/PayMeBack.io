<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'DBConnection.php';

if (!isset($_SESSION['username'])) {
	header("location: index.php");
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
		//TODO direct to 404 here
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

