<div class="row mt-5">
    <div class="col-auto" id="content-left">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createEventModal">
            Create event
        </button>
		<?php
		include "eventCreationModal.php"; ?>
    </div>
    <div class="col" id="content-right">
        <p class="h2">My events</p>
        <?php
		$events = DBConnection::getInstance()->getAllEventsForUser($_SESSION['username']);
        echo '<div class="mt-3">';
        if (count($events) == 0) {
            echo '<p class="lead">You don\'t have any events</p>';
        } else {
            echo '<ul class="list-group">';
            foreach ($events as $event) {
                echo '<a href="events.php?id=' . $event['event_id'] . '" class="list-group-item list-group-item-action">' . $event['event_name'] . '</a>';
            }
            echo '</ul>';
        }
        echo '</div>';
        ?>
    </div>
</div>