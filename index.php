<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/includes/db.php';

$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- User Management Section -->
<div class="container mt-5">
    <h2>User Management</h2>
    <div class="mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#userModal" id="addUserBtn">Add</button>
        <label for="userActions"></label>
        <select id="userActions" class="ml-2">
            <option>-Please Select-</option>
            <option value="set_active">Set Active</option>
            <option value="set_not_active">Set Not Active</option>
            <option value="delete">Delete</option>
        </select>
        <button class="btn btn-success" id="applyActionBtn">OK</button>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>
                <label for="selectAll">
                    <input type="checkbox" id="selectAll">
                </label>
            </th>
            <th>Name</th>
            <th>Status</th>
            <th>Role</th>
            <th>Options</th>
        </tr>
        </thead>
        <tbody id="userTableBody">
<!--        here is the table-->
        </tbody>
    </table>


    <div class="mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#userModal" id="addUserBtnBottom">Add</button>
        <label for="userActionsBottom"></label>
        <select id="userActionsBottom" class="ml-2">
            <option>-Please Select-</option>
            <option value="set_active">Set Active</option>
            <option value="set_not_active">Set Not Active</option>
            <option value="delete">Delete</option>
        </select>
        <button class="btn btn-success" id="applyActionBtnBottom">OK</button>
    </div>

</div>

<!-- Modal for Add/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm" method="POST">
                    <input type="hidden" id="userId" name="userId">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName">
                        <small class="text-danger" id="firstNameError" style="display: none;">This field is required.</small>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName">
                        <small class="text-danger" id="lastNameError" style="display: none;">This field is required.</small>
                    </div>
                    <div class="form-group">
                        <label for="statusSwitch">Status</label><br>
                        <label class="switch">
                            <input type="checkbox" id="statusSwitch" name="status" value="active">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">-Please select-</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        <small class="text-danger" id="roleError" style="display: none;">Please choose a role from the list.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Deleting User Confirmation -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the following users?</p>
                <ul id="userListToDelete"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Unified Modal for Custom Warnings -->
<div class="modal fade" id="customWarningModal" tabindex="-1" role="dialog" aria-labelledby="customWarningModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customWarningModalLabel">Warning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="customWarningMessage">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>


<script src="assets/script.js"></script>
</body>
</html>
