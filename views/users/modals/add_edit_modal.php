<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="userModalError" class="alert alert-danger d-none"></div>
                <form id="userForm" method="POST">
                    <input type="hidden" id="userId" name="userId">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName">
                        <small class="form-text text-danger d-none" id="firstNameError">This field is required.</small>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName">
                        <small class="form-text text-danger d-none" id="lastNameError">This field is required.</small>
                    </div>
                    <div class="mb-3">
                        <label for="statusSwitch" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="statusSwitch">
                            <label class="form-check-label" for="statusSwitch"></label>
                        </div>
                        <input type="hidden" id="status" name="status" value="0">
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <option value="0">Please select</option>
                            <?php foreach ($roles as $roleId => $roleName): ?>
                                <option value="<?= htmlspecialchars($roleId) ?>"><?= htmlspecialchars($roleName) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-danger d-none" id="roleError">Please choose a role from the list.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn" form="userForm">Save</button>
            </div>
        </div>
    </div>
</div>