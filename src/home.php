<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once 'DBConnection.php';
require_once 'DataValidator.php';

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
    <link rel="stylesheet" href="css/custom.css">

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
<?php
include "navbar.html";
?>
<div class="container mt-5">
    <div class="container mt-5" id="content">
        <p class="lead">Hello world from home</p>
        <p class="lead">Total balance
            is <?php echo DBConnection::getInstance()->getTotalBalanceForUser($_SESSION['username']); ?></p>
    </div>
</div>
<?php
include "footer.html"
?>
<script type="text/javascript">
    $("#homeLink").toggleClass("active");
</script>

</body>
