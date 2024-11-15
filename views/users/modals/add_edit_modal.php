<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="userModalError" class="alert alert-danger" style="display: none;"></div>
                <form id="userForm" method="POST">
                    <input type="hidden" id="userId" name="userId">
                    <div class="form-group mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName">
                        <small class="form-text text-danger error-message" id="firstNameError">This field is required.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName">
                        <small class="form-text text-danger error-message" id="lastNameError">This field is required.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="statusSwitch" class="form-label">Status</label>
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="statusSwitch">
                            <label class="form-check-label" for="statusSwitch"></label>
                        </div>
                        <input type="hidden" id="status" name="status" value="0">
                    </div>
                    <div class="form-group mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id">
                            <option value="0">-Please select-</option>
                            <option value="1">Admin</option>
                            <option value="2">User</option>
                        </select>
                        <small class="form-text text-danger error-message" id="roleError">Please choose a role from the list.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn" form="userForm">Save</button>
            </div>
        </div>
    </div>
</div>