<?php
session_start();
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
</head>
<body>
<div class="container mt-5">
    <?php
        include "navbar.html";
    ?>
    <div class="container mt-5" id="content">
        <p class="lead">Hello world from home</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
        integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>


<script type="text/javascript">
    $("#homeLink").toggleClass("btn-outline-primary");
    $("#homeLink").toggleClass("btn-primary");
</script>

</body>
