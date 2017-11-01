<div class="modal fade" id="createExpenseModal" tabindex="-1" role="dialog" aria-labelledby="createExpenseModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create new expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
			<?php
			$redraw = isset($_SESSION['showExpenseModal']) && $_SESSION['showExpenseModal'];
			$people = DBConnection::getInstance()->selectUsersForEvent($event['event_id']);
			?>
            <form action="../events.php" method="post">
                <div class="modal-body">
                    <div class="form-group" hidden>
                        <label for="eventID">Event ID</label>
                        <input type="text" class="form-control" id="event_id" name="event_id"
                               value="<?php echo $event['event_id']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="expenseTitle">Title of the expense</label>
						<?php
						$value = "";
						$class = "form-control";
						if ($redraw) {
							$value = $_SESSION['expenseTitle'];
							if (!$_SESSION['validExpenseTitle']) {
								$class .= " is-invalid";
							}
						}
						echo '<input type="text" class="' . $class . '" id="expenseTitle" name="expenseTitle" value="' . $value . '" required>'
						?>
                        <small id="titleHelp" class="form-text text-muted">The title of your expense must be less
                            than 256 characters long.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="expenseDescription">Description of the expense</label>
						<?php
						$value = "";
						$class = "form-control";
						if ($redraw) {
							$value = $_SESSION['expenseDescription'];
						}
						echo '<textarea class="form-control" name="expenseDescription" id="expenseDescription" rows="4">' . $value . '</textarea>'
						?>
                    </div>
                    <div class="form-group">
                        <label for="expenseAmount">How much is the expense?</label>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $event['currency_code']; ?></span>
							<?php
							$currency = DBConnection::getInstance()->getCurrency($event['currency_code']);
							$value = "";
							$placeholder = $currency['rounding_multiple'] < 1 ? "0.00" : "0";
							$class = "form-control";
							if ($redraw) {
								$value = $_SESSION['expenseAmount'];
								if (!$_SESSION['validExpenseAmount']) {
									$class .= " is-invalid";
								}
							}
							echo '<input type="text" class="' . $class . '" name="expenseAmount" id="expenseAmount" placeholder="' . $placeholder . '" value="' . $value . '" required>'
							?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="expenseDate">Date of the expense</label>
						<?php
						$value = date('Y-m-d');
						$class = "form-control";
						if ($redraw) {
							$value = $_SESSION['expenseDate'];
							if (!$_SESSION['validExpenseDate']) {
								$class .= " is-invalid";
							}
						}
						echo '<input type="date" value="' . $value . '" class="' . $class . '" id="expenseDate" name="expenseDate" required>'
						?>
                    </div>
                    <div class="form-group">
                        <label for="expenseMaker">Who paid for it?</label>
						<?php
						$userSelected = $_SESSION['username'];
						$class = "custom-select form-control";
						if ($redraw) {
							$userSelected = $_SESSION['expenseMaker'];
							if (!$_SESSION['validExpenseMaker']) {
								$class .= " is-invalid";
							}
						}

						echo '<select class="' . $class . '" id="expenseMaker" name="expenseMaker">';
						foreach ($people as $person) {
							if ($person['username'] == $userSelected) {
								echo '<option value="' . $person['username'] . '" selected="selected">';
							} else {
								echo '<option value="' . $person['username'] . '">';
							}
							echo $person['first_name'] . ' ' . $person['last_name'] . '</option>';
						}
						echo '</select>';
						?>
                    </div>
                    <div class="form-group">
                        <label for="expensePeople">People involved in the expense</label>
						<?php
						foreach ($people as $person) {
							echo '<div class="form-check">';
							echo '<label class="form-check-label">';
							echo '<input class="form-check-input" type="checkbox" name=check-' . $person['username'] . ' checked>';
							echo '&nbsp' . $person['first_name'] . ' ' . $person['last_name'];
							echo '</label>';
							echo '</div>';
						}
						?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="newExpense" name="newExpense">Create
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>