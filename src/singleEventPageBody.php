<?php
$eventQuery = $db->prepare('SELECT * FROM t_events WHERE event_id=:id');
$eventQuery->bindParam(':id', $_GET['id']);
$eventQuery->execute();
$event = $eventQuery->fetch(PDO::FETCH_ASSOC);
?>
<div class="row mt-5">
    <div class="col-auto" id="content-left">
        <a href="events.php" class="btn btn-outline-danger">&lt; Back to list</a>
        <br>
        <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#createExpenseModal">
            New expense
        </button>
        <?php include 'expenseCreationModal.php'; ?>
    </div>
    <div class="col" id="content-right">
        <h1><?php echo $event['event_name']; ?></h1>
        <p><?php echo $event['event_description']; ?></p>
    </div>
</div>
