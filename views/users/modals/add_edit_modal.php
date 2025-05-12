<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel">
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

                    <div id="dynamicFieldsContainer"></div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn" form="userForm">Save</button>
            </div>
        </div>
    </div>
</div>
