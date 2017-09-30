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
			$people = DBConnection::getInstance()->selectUsersForEvent($event['event_id']);
            ?>
            <form action="createExpense.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="expenseTitle">Title of the expense</label>
                        <input type="text" class="form-control" id="expenseTitle" name='expenseTitle' required>
                        <small id="titleHelp" class="form-text text-muted">The title of your expense must be less
                            than 256 characters long.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="expenseDescription">Description of the expense</label>
                        <textarea class="form-control" name="expenseDescription" id="expenseDescription"
                                  rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="expenseAmount">How much is the expense?</label>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $event['currency_code']; ?></span>
                            <input type="text" class="form-control" name="expenseAmount" id="expenseAmount"
                                   placeholder="0.00" required>
                        </div>
                    </div>
                    <!--TODO check this, because it could cause a problem-->
                    <div class="form-group">
                        <label for="expenseDate">Date of the expense</label>
                        <input type="text" value="<?php echo date('Y-m-d') ?>" class="form-control" id="expenseDate"
                               name="expenseDate" required>
                        <small id="dateFormatHelp" class="form-text text-muted">Use format YYYY-mm-dd for dates</small>
                    </div>
                    <div class="form-group">
                        <label for="expenseMaker">Who paid for it?</label>
                        <?php
                        echo '<select class="form-control" id="expenseMaker" name="expenseMaker">';
                        foreach ($people as $person) {
                            if ($person['username'] == $_SESSION['username']) {
                                echo '<option selected="selected">';
                            } else {
                                echo '<option>';
                            }
                            echo $person['username'] . '</option>';
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
                            echo '&nbsp' . $person['username'];
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