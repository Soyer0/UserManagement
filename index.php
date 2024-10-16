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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2>User Management</h2>
    <div class="mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#userModal" id="addUserBtn">Add User</button>
        <label for="userActions"></label><select id="userActions" class="ml-2">
            <option>Please Select</option>
            <option value="set_active">Set Active</option>
            <option value="set_not_active">Set Not Active</option>
            <option value="delete">Delete</option>
        </select>
        <button class="btn btn-success" id="applyActionBtn">OK</button>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th><label for="selectAll"></label><input type="checkbox" id="selectAll"></th>
            <th>Name</th>
            <th>Status</th>
            <th>Role</th>
            <th>Options</th>
        </tr>
        </thead>
        <tbody id="userTableBody">
        <?php foreach ($users as $user): ?>
            <tr data-id="<?= $user['id'] ?>">
                <td><label>
                        <input type="checkbox" class="userCheckbox">
                    </label></td>
                <td><?= htmlspecialchars($user['name_first'] . ' ' . $user['name_last']) ?></td>
                <td>
                        <span class="badge <?= $user['status'] ? 'badge-success' : 'badge-secondary' ?>">
                            <?= $user['status'] ? 'Active' : 'Not Active' ?>
                        </span>
                </td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm editUserBtn" data-toggle="modal" data-target="#userModal" data-id="<?= $user['id'] ?>">Edit</button>
                    <button class="btn btn-danger btn-sm deleteUserBtn" data-id="<?= $user['id'] ?>">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mb-3">
        <button class="btn btn-primary" data-toggle="modal" data-target="#userModal" id="addUserBtn">Add User</button>
        <label for="userActionsBottom"></label>
        <select id="userActionsBottom" class="ml-2">
            <option>Please Select</option>
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
                <h5 class="modal-title" id="userModalLabel">Add/Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="userId">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">Active</option>
                            <option value="0">Not Active</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/script.js"></script>

</body>
</html>
