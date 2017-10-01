<?php
$id = $_GET['id'];
//TODO if event not found, redirect to 404
$event = DBConnection::getInstance()->selectSingleEventByID($id);
?>
<div class="row mt-5">
    <div class="col-auto" id="content-left">
        <a href="events.php" class="btn btn-outline-danger btn-block">&lt; Back to list</a>
        <br>
        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#createExpenseModal">
            New expense
        </button>
        <br>
        <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                data-target="#deleteConfirmationModal">Delete Event
        </button>
		<?php
		include 'expenseCreationModal.php';
		include 'deleteConfirmationModal.php';
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
		$balance = DBConnection::getInstance()->getBalanceForEvent($event['event_id']);
		$users = DBConnection::getInstance()->selectUsersForEvent($event['event_id']);
		echo '<table class="table">';
		echo '<thead>';
		echo '<tr>';
		echo '<th></th>';
		foreach ($users as $user) {
			echo '<th>' . $user['username'] . '</th>';
		}
		echo '</tr>';
		for ($i = 0; $i < count($users); $i++) {
			echo '<tr>';
			for ($j = 0; $j <= count($users); $j++) {
				echo '<th>';
				if ($j == 0) {
					echo $users[$i]['username'];
				} else {
					if ($i == $j - 1) {
						echo "-";
					} else {
						if (!isset($balance[$users[$i]['username']][$users[$j - 1]['username']])) {
							echo 0;
						} else {
							$value = $balance[$users[$i]['username']][$users[$j - 1]['username']];
							$class = $value < 0 ? "text-danger" : "text-success";
							echo '<span class="' . $class . '">' . $value . '</span>';
						}
					}
				}
				echo '</th>';
			}
			echo '</tr>';
		}
		echo '</thead>';
		echo '</table>';
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
