<?php
$id = $_GET['id'];
$event = DBConnection::getInstance()->selectSingleEventByID($id);
?>
<div class="row mt-5">
    <div class="col-auto" id="content-left">
        <a href="events.php" class="btn btn-outline-danger">&lt; Back to list</a>
        <br>
        <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#createExpenseModal">
            New expense
        </button>
		<?php
		include 'expenseCreationModal.php';
		?>
    </div>
    <div class="col" id="content-right">
        <h1><?php echo $event['event_name']; ?></h1>
        <p><?php echo $event['event_description']; ?></p>

        <?php
        
        //TODO have a list of the last 3 expenses and a button to expand all using jquery. Then we can open each expense in its own modal, and display the total in big, and what everyone owes according to coeff
        ?>
    </div>
</div>
