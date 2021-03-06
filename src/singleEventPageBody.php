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
		include "forms/directPaymentCreationModal.php";
		include 'forms/expenseCreationModal.php';
		include 'delete/deleteConfirmationModal.php';
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
		$total = DBConnection::getInstance()->getTotalExpenseForEvent($event['event_id']);
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
			echo '<div class="mt-2"><a href="#" id="showMoreLink">Show more expenses</a></div>';
		} elseif ($count == 0) {
			echo '<p>No expenses added to this event yet</p>';
		}
		echo '<ul class="list-group mt-2">';
		echo '<strong><li class="list-group-item list-group-item-dark"><div class="row"><div class="col">TOTAL</div><div class="col-auto">' . $total . ' ' . $event['currency_code'] . '</div></div></li></strong>';
		echo '</ul>';
		echo '<h2>Payments by user</h2>';
		$byUser = DBConnection::getInstance()->getAllExpensesForEventByUser($event['event_id']);
		if ($byUser == null) {
			echo 'No expenses added to this event yet';
		}
		echo '<ul class="list-group">';
		foreach ($byUser as $userTotal) {
			$fullName = DBConnection::getInstance()->getFullNameForUser($userTotal['buyer_username']);
			echo '<li class="list-group-item"><div class="row"><div class="col">' . $fullName . '</div><div class="col-auto">' . $userTotal['sum'] . ' ' . $event['currency_code'] . '</div></div></li>';
		}
		echo '</ul>';
		echo '<h2>Latest direct payments</h2>';
		$reimbursements = DBConnection::getInstance()->getAllDirectPaymentsForEvent($event['event_id']);
		$directCount = 0;
		if (count($reimbursements) > 0) {
			echo '<ul class="list-group">';
			foreach ($reimbursements as $reimbursement) {
				$classToAdd = $directCount > 2 ? " removable" : "";
				$displayToAdd = $directCount > 2 ? 'style="display: none"' : '';
				$userPaying = DBConnection::getInstance()->getFullNameForUser($reimbursement['paying_username']);
				$userPayed = DBConnection::getInstance()->getFullNameForUser($reimbursement['payed_username']);
				echo '<button id="' . $reimbursement['reimbursement_id'] . '" class="list-group-item list-group-item-action direct-payment' . $classToAdd . '" ' . $displayToAdd . '><div class="row"><div class="col">' . $userPaying . ' => ' . $userPayed . '</div><div class="col-auto">' . $reimbursement['amount'] . ' ' . $event['currency_code'] . '</div></div></button>';
				$directCount++;
			}
			if ($directCount > 3) {
				echo '<div class="mt-2"><a href="#" id="showMoreDirectLink">Show more direct payments</a></div>';
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
			echo '<th>' . DBConnection::getInstance()->getFullNameForUser($user['username']) . '</th>';
		}
		echo '</tr>';
		for ($i = 0; $i < count($users); $i++) {
			echo '<tr>';
			for ($j = 0; $j <= count($users); $j++) {
				echo '<th>';
				if ($j == 0) {
					echo DBConnection::getInstance()->getFullNameForUser($users[$i]['username']);
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
            let hiddenExpense = true;
            let hiddenDirect = true;
            $('#showMoreLink').click(function () {
                console.log("EXPENSE CLICK");
                $(".expense.removable").toggle();
                hiddenExpense = !hiddenExpense;
                $('#showMoreLink').html(hiddenExpense ? "Show more expenses" : "Show less expenses");
            });

            $('#showMoreDirectLink').click(function () {
                console.log("DIRECT CLICK");
                $(".direct-payment.removable").toggle();
                hiddenDirect = !hiddenDirect;
                $('#showMoreDirectLink').html(hiddenDirect ? "Show more direct payments" : "Show less direct payments");
            });
            $('.expense').click(function () {
                let id = $(this).attr('id');
                $.ajax({
                    url: 'modalViews/singleExpenseModal.php',
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
                    url: 'modalViews/singleDirectPaymentModal.php',
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
		if (isset($_SESSION['showDirectPaymentModal']) && $_SESSION['showDirectPaymentModal']) {
			echo '<script type="text/javascript">$("#createReimbursementModal").modal()</script>';
			$_SESSION['showDirectPaymentModal'] = false;
		}
		?>
    </div>
</div>
