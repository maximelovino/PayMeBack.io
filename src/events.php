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
    <?php
    include "navbar.html";
    ?>
    <div class="row mt-5">
        <div class="col-auto" id="content-left">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createEventModal">
                Create event
            </button>
            <div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create new event</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="createEvent.php" method="post">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="eventTitle">Title of the event</label>
                                    <input type="text" class="form-control" id="eventTitle" name='eventTitle' required>
                                    <small id="titleHelp" class="form-text text-muted">The title your event must be less
                                        than 256 characters long.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="eventDescription">Description of the event</label>
                                    <textarea class="form-control" name="eventDescription" id="eventDescription"
                                              rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="eventCurrency">Currency for the event</label>
                                    <select name="eventCurrency" id="eventCurrency" class="form-control" required>
                                        <?php
                                        $currencies = $db->query("SELECT currency_code FROM t_currencies ORDER BY currency_code");

                                        foreach ($currencies as $currency) {
                                            echo '<option>' . $currency['currency_code'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="eventUsers">Who are you going with?</label>
                                    <select multiple name="eventUsers[]" id="eventUsers" class="form-control" required>
                                        <?php
                                        $usersQuery = $db->prepare("SELECT username FROM t_users where username != :username ORDER BY username");
                                        $usersQuery->bindParam(':username', $_SESSION['username']);
                                        $usersQuery->execute();
                                        $users = $usersQuery->fetchAll();

                                        foreach ($users as $user) {
                                            echo '<option>' . $user['username'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="newEvent" name="newEvent">Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" id="content-right">
            <p class="h2">My events</p>
            <?php
            $eventsQuery = $db->prepare("SELECT * from t_group_membership JOIN t_events on t_events.group_id = t_group_membership.group_id HAVING username=:username");
            $eventsQuery->bindParam(":username", $_SESSION['username']);
            $eventsQuery->execute();
            $events = $eventsQuery->fetchAll(PDO::FETCH_ASSOC);
            echo '<div class="mt-3">';
            if (count($events) == 0) {
                echo '<p class="lead">You don\'t have any events</p>';
            } else {
                echo '<ul class="list-group">';
                foreach ($events as $event) {
                    echo '<li class="list-group-item">' . $event['event_name'] . '</li>';
                }
                echo '</ul>';
            }
            echo '</div>';
            ?>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"
        integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
    $("#eventsLink").toggleClass("btn-outline-dark");
    $("#eventsLink").toggleClass("btn-dark");
</script>

<script type="text/javascript">
    $('.list-group-item').hover(function () {
        $(this).toggleClass('active');
    })
</script>
</body>


