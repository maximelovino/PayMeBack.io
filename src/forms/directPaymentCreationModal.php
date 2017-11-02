<div class="modal fade" id="createReimbursementModal" tabindex="-1" role="dialog"
     aria-labelledby="createReimbursementModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create new reimbursement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
			<?php
			$redraw = isset($_SESSION['showDirectPaymentModal']) && $_SESSION['showDirectPaymentModal'];
			$people = DBConnection::getInstance()->selectUsersForEvent($event['event_id']);
			?>
            <form action="../events.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paying_user">Who is paying?</label>
						<?php
						$valueToMatch = $_SESSION['username'];
						$class = "custom-select form-control";
						if ($redraw) {
							$valueToMatch = $_SESSION['payingUser'];
							if (!$validReimbursementPayer) {
								$class .= " is-invalid";
							}
						}
						echo '<select name="paying_user" id="paying_user" class="' . $class . '">';
						foreach ($people as $user) {
							if ($user['username'] == $valueToMatch) {
								echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '" selected>' . DBConnection::getInstance()->getFullNameForUser($user['username']) . '</option>';
							} else {
								echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '">' . DBConnection::getInstance()->getFullNameForUser($user['username']) . '</option>';
							}
						}
						?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payed_user">Who is being payed?</label>
						<?php
						$valueToMatch = $_SESSION['username'];
						$class = "custom-select form-control";
						if ($redraw) {
							$valueToMatch = $_SESSION['payedUser'];
							if (!$validReimbursementPayed) {
								$class .= " is-invalid";
							}
						}
						echo '<select name="payed_user" id="payed_user" class="' . $class . '">';
						foreach ($people as $user) {
							if ($user['username'] == $valueToMatch) {
								echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '" selected>' . DBConnection::getInstance()->getFullNameForUser($user['username']) . '</option>';
							} else {
								echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '">' . DBConnection::getInstance()->getFullNameForUser($user['username']) . '</option>';
							}
						}
						?>
                        </select>
                    </div>
                    <div class="form-group" style="display: none">
                        <input type="text" name="event_id" id="event_id" readonly
                               value="<?php echo $event['event_id']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="amount">How much?</label>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $event['currency_code']; ?></span>
							<?php
							$value = "";
							$class = "form-control";
							if ($redraw) {
								$value = $_SESSION['amount'];
								if (!$validReimbursementAmount) {
									$class .= " is-invalid";
								}
							}
							echo '<input type="text" class="' . $class . '" name="amount" id="amount" placeholder="0.00" value="' . $value . '" required>'
							?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date">Date of the expense</label>
						<?php
						$value = date('Y-m-d');
						$class = "form-control";
						if ($redraw) {
							$value = $_SESSION['date'];
							if (!$validReimbursementDate) {
								$class .= " is-invalid";
							}
						}
						echo '<input type="date" value="' . $value . '" class="' . $class . '" id="date" name="date" required>';
						?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="newReimbursement" name="newReimbursement">
                            Create
                        </button>
                    </div>
                    <script type="text/javascript">
                        function getOptionByValue(select, value) {
                            const options = select.options;
                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === value) {
                                    return options[i]
                                }
                            }
                            return null
                        }

                        function disableFromSecondSelect() {
                            const firstSelect = document.querySelector('#paying_user');
                            let value = firstSelect.value;
                            const secondSelect = document.querySelector('#payed_user');
                            for (let i = 0; i < secondSelect.length; i++) {
                                secondSelect.options[i].disabled = false;
                            }
                            let option = getOptionByValue(secondSelect, value);
                            option.disabled = true;
                            option.selected = false;
                        }

                        disableFromSecondSelect();
                        const firstSelect = document.querySelector('#paying_user');
                        firstSelect.onchange = disableFromSecondSelect;
                    </script>
                </div>
            </form>
        </div>
    </div>
</div>