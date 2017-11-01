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
			require_once 'DBConnection.php';
			$people = DBConnection::getInstance()->selectUsersForEvent($event['event_id']);
			?>
            <form action="events.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="paying_user">Who is paying?</label>
                        <select name="paying_user" id="paying_user" class="custom-select form-control">
							<?php
							foreach ($people as $user) {
								if ($user['username'] == $_SESSION['username']) {
									echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '" selected>' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
								} else {
									echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
								}
							}
							?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payed_user">Who is being payed?</label>
                        <select name="payed_user" id="payed_user" class="form-control custom-select">
							<?php
							foreach ($people as $user) {
								if ($user['username'] == $_SESSION['username']) {
									echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
								} else {
									echo '<option id="' . $user['username'] . '" value="' . $user['username'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
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
                            <input type="text" class="form-control" name="amount" id="amount"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date">Date of the expense</label>
                        <input type="date" value="<?php echo date('Y-m-d') ?>" class="form-control" id="date"
                               name="date" required>
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