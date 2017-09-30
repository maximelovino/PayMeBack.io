<div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create new event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="createEvent.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="eventTitle">Title of the event</label>
                        <input type="text" class="form-control" id="eventTitle" name='eventTitle' required>
                        <small id="titleHelp" class="form-text text-muted">The title of your event must be less
                            than 256 characters long.
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="eventDescription">Description of the event</label>
                        <textarea class="form-control" name="eventDescription" id="eventDescription"
                                  rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="eventCurrency">Currency for the event</label>
                        <select name="eventCurrency" id="eventCurrency" class="form-control" required>
							<?php
							$currencies = DBConnection::getInstance()->getAllCurrencies();

							foreach ($currencies as $currency) {
								echo '<option>' . $currency['currency_code'] . '</option>';
							}
							?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="eventUsers">Who are you going with?</label>
                        <select multiple name="eventUsers[]" id="eventUsers" class="form-control" required>
							<?php
							$users = DBConnection::getInstance()->getAllUsers();
							foreach ($users as $user) {
								if ($user['username'] != $_SESSION['username'])
									echo '<option value="' . $user['username'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
							}
							?>
                        </select>
                    </div>
					<?php
					?>
                    <div class="form-group" id="weightsDiv"></div>
                </div>
                <script type="text/javascript">

                    function createDivForUsername(username) {
                        return `<div class="row"><div class="col"><label for="${username}">${username}</label></div><div class="col"><input type="number" value="1" name="weight-${username}" id="weight-${username}"></div></div>`;
                    }

                    function weightsDisplay() {
                        $("#weightsDiv").empty();
                        $("#weightsDiv").append("<label>Weights</label>");
                        let username = "<?php echo $_SESSION['username'];?>";
                        $("#weightsDiv").append(createDivForUsername(username));

                        $("#eventUsers option:selected").each(function () {
                            $("#weightsDiv").append(createDivForUsername($(this).val()));
                        })
                    }

                    $('#createEventModal').on('shown.bs.modal', function () {
                        $('#eventUsers').select2({
                            placeholder: 'Select users for your event...',
                            dropdownParent: $('#createEventModal'),
                        });
                    });
                    $('#eventUsers').change(weightsDisplay).trigger("change");
                </script>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="newEvent" name="newEvent">Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>