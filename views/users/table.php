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
    <?php foreach ($users as $user): ?>
        <tr data-id="<?= $user['id'] ?>">
            <td>
                <input type="checkbox" class="userCheckbox" value="<?= $user['id'] ?>">
            </td>
            <td><?= htmlspecialchars($user['name_first'] . ' ' . $user['name_last']) ?></td>
            <td class="status">
                <span class="status-circle <?= $user['status'] == 1 ? 'active' : '' ?>"></span>
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