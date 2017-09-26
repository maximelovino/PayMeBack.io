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
                            $currencies = $db->query("SELECT currency_code FROM t_currencies ORDER BY currency_code");

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
                            $usersQuery = $db->prepare("SELECT * FROM t_users where username !=:username ORDER BY username");
                            $usersQuery->bindParam(':username', $_SESSION['username']);
                            $usersQuery->execute();
                            $users = $usersQuery->fetchAll();

                            foreach ($users as $user) {
                                echo '<option value="' . $user['username'] . '">' . $user['first_name'] . ' ' . $user['last_name'] . '</option>';
                            }
                            ?>
                        </select>

                        <script type="text/javascript">
                            $('#createEventModal').on('shown.bs.modal', function () {
                                $('#eventUsers').select2({
                                    placeholder: 'Select users for your event...',
                                    dropdownParent: $('#createEventModal'),
                                });
                            })
                        </script>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="newEvent" name="newEvent">Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>