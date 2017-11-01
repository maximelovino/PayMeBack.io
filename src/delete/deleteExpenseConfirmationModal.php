<div class="modal fade" id="deleteExpenseConfirmationModal" tabindex="-1" role="dialog"
     aria-labelledby="deleteExpenseConfirmationModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/delete/deleteExpense.php" method="post">
                <div class="modal-body">
                    <input type="text" name="id" readonly hidden value="<?php echo $expense['transaction_id']; ?>">
                    <p class="lead">Are you sure you want to delete this expense?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="deleteExpense" name="deleteExpense">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>