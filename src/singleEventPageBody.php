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
        <h2>Latest expenses</h2>
		<?php
		$expenses = DBConnection::getInstance()->getAllExpensesForEvent($event['event_id']);
		echo '<ul class="list-group">';
		$count = 0;
		foreach ($expenses as $expense) {
			if ($count > 2) {
				echo '<button class="list-group-item list-group-item-action expense removable" style="display: none"><div class="row"><div class="col">' . $expense['title'] . '</div><div class="col-auto">' . $expense['amount'] . ' ' . $event['currency_code'] . '</div></div></button>';
			} else {
				echo '<button class="list-group-item list-group-item-action expense"><div class="row"><div class="col">' . $expense['title'] . '</div><div class="col-auto">' . $expense['amount'] . ' ' . $event['currency_code'] . '</div></div></button>';
			}
			$count++;
		}
		echo '</ul>';
		if ($count > 3) {
			echo '<a href="#" id="showMoreLink">Show more expenses</a>';
		} elseif ($count == 0) {
			echo '<p>No expenses added to this event yet</p>';
		}
		echo '<h2>Balance</h2>';
		foreach ($expenses as $expense) {
			$balances = DBConnection::getInstance()->getExpensesByUserForExpense($expense['transaction_id']);
			echo '<br>';
			print_r($balances);
			echo '<br>';
		}
		?>
        <script type="text/javascript">
            //TODO this
            let hidden = true;
            $('#showMoreLink').click(function () {
                $(".removable").toggle();
                hidden = !hidden;
                $('#showMoreLink').html(hidden ? "Show more expenses" : "Show less expenses");
            })
        </script>
    </div>
</div>
