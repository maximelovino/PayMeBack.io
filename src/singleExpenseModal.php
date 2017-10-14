<?php
require_once "DBConnection.php";
$id = $_GET['expense'];
$expense = DBConnection::getInstance()->getSingleExpenseDetail($id);
$balance = DBConnection::getInstance()->getExpensesByUserForExpense($id);
$event = DBConnection::getInstance()->selectSingleEventByID($expense['event_id']);
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
						echo '<tr><td>' . $user . '</td><td>' . $amount . ' ' . $event['currency_code'] . '</td></tr>';
					}
					?>
                </table>
            </div>
            <div class="modal-footer">
				<?php
				//TODO code the modal to confirm deletion of expense
				?>
                <button type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#deleteExpenseConfirmation">Remove expense
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>