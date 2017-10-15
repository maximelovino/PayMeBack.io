<?php
session_start();
if (!isset($_SESSION['username'])) {
	exit(0);
}
require_once "DBConnection.php";
require_once "DataValidator.php";
$id = $_GET['directPayment'];
$payment = DBConnection::getInstance()->getDirectPayment($id);
$payingUser = DBConnection::getInstance()->getSingleUser($payment['paying_username']);
$payedUser = DBConnection::getInstance()->getSingleUser($payment['payed_username']);
$event = DBConnection::getInstance()->selectSingleEventByID($payment['event_id']);

if (!DataValidator::hasUserAccessToEvent($_SESSION['username'], $payment['event_id'])) {
	exit(0);
}
include 'deleteDirectPaymentConfirmationModal.php'
?>

<div class="modal fade" id="singleDirectPaymentModal" tabindex="-1" role="dialog"
     aria-labelledby="singleDirectPaymentModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
					<?php echo 'Direct payment ' . $payment['reimbursement_id']; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
				<?php
				echo '<p class="lead">Direct payment of ' . $payment['amount'] . ' ' . $event['currency_code'] . ' from ' . $payingUser['first_name'] . ' ' . $payingUser['last_name'] . ' to ' . $payedUser['first_name'] . ' ' . $payedUser['last_name'] . '</p>'
				?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" data-toggle="modal"
                        data-target="#deleteDirectPaymentConfirmationModal">Remove direct payment
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>