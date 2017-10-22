<?php
$id = $_GET['id'];
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
        <button type="button" class="btn btn-secondary btn-block" data-toggle="modal"
                data-target="#createReimbursementModal">New direct payment
        </button>
        <br>
        <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                data-target="#deleteConfirmationModal">Delete Event
        </button>
		<?php
		include "reimbursementModal.php";
		include 'expenseCreationModal.php';
		include 'deleteConfirmationModal.php';
		?>
        <div id="expenseModalPlaceHolder"></div>
        <div id="directPaymentModalPlaceHolder"></div>
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
			$classToAdd = $count > 2 ? " removable" : "";
			$displayToAdd = $count > 2 ? 'style="display: none"' : '';
			echo '<button id="' . $expense['transaction_id'] . '" class="list-group-item list-group-item-action expense' . $classToAdd . '" ' . $displayToAdd . '><div class="row"><div class="col">' . $expense['title'] . '</div><div class="col-auto">' . $expense['amount'] . ' ' . $event['currency_code'] . '</div></div></button>';
			$count++;
		}
		echo '</ul>';
		if ($count > 3) {
			echo '<a href="#" id="showMoreLink">Show more expenses</a>';
		} elseif ($count == 0) {
			echo '<p>No expenses added to this event yet</p>';
		}
		echo '<h2>Latest direct payments</h2>';
		$reimbursements = DBConnection::getInstance()->getAllDirectPaymentsForEvent($event['event_id']);
		//TODO display expendable list like expenses
		if (count($reimbursements) > 0) {
			echo '<ul class="list-group">';
			foreach ($reimbursements as $reimbursement) {
				$userPaying = DBConnection::getInstance()->getSingleUser($reimbursement['paying_username']);
				$userPayed = DBConnection::getInstance()->getSingleUser($reimbursement['payed_username']);
				echo '<button id="' . $reimbursement['reimbursement_id'] . '" class="list-group-item list-group-item-action direct-payment"><div class="row"><div class="col">' . $userPaying['first_name'] . ' ' . $userPaying['last_name'] . ' => ' . $userPayed['first_name'] . ' ' . $userPayed['last_name'] . '</div><div class="col-auto">' . $reimbursement['amount'] . ' ' . $event['currency_code'] . '</div></div></button>';
			}
		} else {
			echo '<p>No direct payments made</p>';
		}
		echo '</ul>';
		echo '<h2>Balance</h2>';
		$balance = DBConnection::getInstance()->getBalanceForEvent($event['event_id']);
		$users = DBConnection::getInstance()->selectUsersForEvent($event['event_id']);
		echo '<table class="table">';
		echo '<thead>';
		echo '<tr>';
		echo '<th></th>';
		foreach ($users as $user) {
			echo '<th>' . $user['first_name'] . ' ' . $user['last_name'] . '</th>';
		}
		echo '</tr>';
		for ($i = 0; $i < count($users); $i++) {
			echo '<tr>';
			for ($j = 0; $j <= count($users); $j++) {
				echo '<th>';
				if ($j == 0) {
					echo $users[$i]['first_name'] . ' ' . $users[$i]['last_name'];
				} else {
					if ($i == $j - 1) {
						echo "-";
					} else {
						if (!isset($balance[$users[$i]['username']][$users[$j - 1]['username']])) {
							echo 0;
						} else {
							$value = $balance[$users[$i]['username']][$users[$j - 1]['username']];
							$class = $value < 0 ? "text-danger" : ($value != 0 ? "text-success" : "");
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
            let hidden = true;
            $('#showMoreLink').click(function () {
                $(".removable").toggle();
                hidden = !hidden;
                $('#showMoreLink').html(hidden ? "Show more expenses" : "Show less expenses");
            });
            $('.expense').click(function () {
                let id = $(this).attr('id');
                $.ajax({
                    url: 'singleExpenseModal.php',
                    data: 'expense=' + id,
                    success: function (data) {
                        $('#expenseModalPlaceHolder').html(data);
                        $('#singleExpenseModal').modal();
                    },
                    type: 'GET',
                });
            });
            $('.direct-payment').click(function () {
                let id = $(this).attr('id');
                console.log(id);
                $.ajax({
                    url: 'singleDirectPaymentModal.php',
                    data: 'directPayment=' + id,
                    success: (data) => {
                        $('#directPaymentModalPlaceHolder').html(data);
                        $('#singleDirectPaymentModal').modal()
                    },
                    type: 'GET',
                })
            });
        </script>
		<?php

		if (isset($_SESSION['showExpenseModal']) && $_SESSION['showExpenseModal']) {
			echo '<script type="text/javascript">$("#createExpenseModal").modal()</script>';
			$_SESSION['showExpenseModal'] = false;
		}
		?>
    </div>
</div>
