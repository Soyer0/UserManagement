<div class="mb-3 d-flex align-items-center col-3">
    <label for="userSearchInput" class="mb-0 me-2"></label>
    <div class="input-group">
        <input type="text" id="userSearchInput" class="form-control" placeholder="Search users by name...">
        <button class="btn btn-outline-secondary" id="searchBtn" type="button">
            <i class="bi bi-search"></i>
        </button>
    </div>
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

            <?php foreach ($columns as $column): ?>
                <td class="<?= $column ?>">
                    <?php
                    $value = $user[$column] ?? null;
                    if ($column === 'status') {
                        echo '<span class="status-circle ' . ($status[$value]) . '"></span>';
                    } elseif ($column === 'role_id') {
                        echo $roles[$value] ?? '-';
                    } else {
                        echo $value !== null && trim($value) !== '' ? htmlspecialchars($value) : '-';
                    }
                    ?>
                </td>
            <?php endforeach; ?>

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
