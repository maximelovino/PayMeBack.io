<?php
session_start();
if (!isset($_SESSION['username'])) {
	exit(0);
}
require_once "DBConnection.php";
require_once "DataValidator.php";
$id = $_GET['expense'];
$expense = DBConnection::getInstance()->getSingleExpenseDetail($id);
$balance = DBConnection::getInstance()->getExpensesByUserForExpense($id);
$event = DBConnection::getInstance()->selectSingleEventByID($expense['event_id']);
if (!DataValidator::hasUserAccessToEvent($_SESSION['username'], $event['event_id'])) {
	exit(0);
}
include 'deleteExpenseConfirmationModal.php'
?>

<div class="modal fade" id="singleExpenseModal" tabindex="-1" role="dialog"
     aria-labelledby="singleExpenseModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
					<?php echo 'Expense ' . $expense['transaction_id']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h2><?php echo $expense['title']; ?></h2>
                <p class="lead"><?php echo $expense['description']; ?></p>
                <div class="row">
                    <div class="col"><h3>Price</h3></div>
                    <div class="col-auto"><p
                                class="btn btn-outline-primary"><?php echo $expense['amount'] . ' ' . $event['currency_code']; ?></p>
                    </div>
                    <div class="col-auto"><p class="btn btn-outline-dark"><?php echo $expense['date']; ?></p></div>
                </div>
                <table class="table">
					<?php
					foreach ($balance as $user => $amount) {
						$fullUser = DBConnection::getInstance()->getSingleUser($user);
						echo '<tr><td>' . $fullUser['first_name'] . ' ' . $fullUser['last_name'] . '</td><td>' . $amount . ' ' . $event['currency_code'] . '</td></tr>';
					}
					?>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" data-toggle="modal"
                        data-target="#deleteExpenseConfirmationModal">Remove expense
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>