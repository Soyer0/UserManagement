<div class="mb-3">
    <label for="userSearchInput"><input type="text" id="userSearchInput" class="form-control" placeholder="Search users by name...">
    </label>
</div>

<table class="table table-bordered">
    <thead>
    <tr>
        <th>
            <label for="selectAll">
                <input type="checkbox" id="selectAll">
            </label>
        </th>
        <?php foreach ($columns as $label): ?>
            <th><?= htmlspecialchars($label) ?></th>
        <?php endforeach; ?>
        <th>Options</th>
    </tr>
    </thead>
    <tbody id="userTableBody">
    <?php foreach ($users as $user): ?>
        <tr data-id="<?= $user['id'] ?>">
            <td>
                <input type="checkbox" class="userCheckbox" value="<?= $user['id'] ?>">
            </td>
            <td><?= htmlspecialchars($user['name_first']) ?></td>
            <td><?= htmlspecialchars($user['name_last']) ?></td>
            <td class="status">
                <span class="status-circle <?= $status[$user['status']] ?>"></span>
            </td>
            <td><?= $roles[$user['role_id']] ?></td>
            <td>
                <button class="btn btn-warning btn-sm editUserBtn" data-id="<?= $user['id'] ?>">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm deleteUserBtn" data-id="<?= $user['id'] ?>">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    window.userColumns = <?= json_encode($columns) ?>;
</script>
