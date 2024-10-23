$(document).ready(function () {
// Opens and configures the user modal for adding or editing a user.
    function openUserModal(mode, user) {
        $('#userForm')[0].reset();
        $('#userId').val(user ? user.id : '');
        $('#userModalLabel').text(mode === 'edit' ? 'Edit User' : 'Add User');
        $('#submitBtn').text(mode === 'edit' ? 'Update' : 'Save');


        if (user) {
            $('#firstName').val(user.name_first);
            $('#lastName').val(user.name_last);
            $('#statusSwitch').prop('checked', user.status === 1);
            $('#role_id').val(user.role_id);
        } else {
            $('#statusSwitch').prop('checked', false);
            $('#role_id').val(0);
        }

        if (mode === 'edit') {
            $('#role_id option[value=0]').hide();
        } else {
            $('#role_id option[value=0]').show();
        }
        $('#userModal').modal('show');
    }

    // Displays a custom warning modal with the provided message.
    function showCustomWarning(message) {
        document.getElementById('customWarningMessage').innerText = message;
        $('#customWarningModal').modal('show');
    }

    // Binds a click event to the "Add User" buttons (both top and bottom).
    $('#addUserBtn, #addUserBtnBottom').on('click', function () {
        openUserModal('add');
    });

    //  Binds a click event to elements with the class "editUserBtn".
    $(document).on('click', '.editUserBtn', function () {
        const userId = $(this).data('id');
        $.get('ajax/get_user.php', {id: userId }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                openUserModal('edit', result.user);
            } else {
                alert(result.error.message);
            }
        });
    });

    // Function for validating the user form
    function validateUserForm() {
        $('#firstNameError, #lastNameError, #roleError').hide();

        const firstName = $('#firstName').val().trim();
        const lastName = $('#lastName').val().trim();
        const selectedRole = $('#role_id').val();

        let isValid = true; // Flag for form validity

        if (!firstName) {
            $('#firstNameError').show();
            isValid = false;
        }

        if (!lastName) {
            $('#lastNameError').show();
            isValid = false;
        }

        if (!selectedRole) {
            $('#roleError').show();
            isValid = false;
        }

        return isValid; // Return the validity status
    }

    // Handles user form submission for adding or updating a user.
    $(document).on('submit', '#userForm', function (e) {
        e.preventDefault();

        if (!validateUserForm()) {
            return;
        }

        const userId = $('#userId').val();
        const action = userId ? 'update' : 'add';
        const url = userId ? 'ajax/edit_user.php' : 'ajax/add_user.php'; // Dynamic URL
        const status = $('#statusSwitch').is(':checked') ? 1 : 0;

        $.post(url, $(this).serialize() + `&status=${status}`, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                $('#userModal').modal('hide');
                if (action === 'add') {
                    $('#userTableBody').append(generateUserRowHtml(result.user));
                } else {
                    $(`tr[data-id="${userId}"]`).replaceWith(generateUserRowHtml(result.user));
                }
                $('#selectAll').prop('checked', false);
            } else {
                alert(result.error.message);
            }
        });
    });

    // Sends a POST request to apply a specified action (e.g., set active, delete) on selected users.
    function    applyUserAction(action, users) {
        const urlMap = {
            'delete': 'ajax/delete_users.php',
            'set_active': 'ajax/set_status.php',
            'set_not_active': 'ajax/set_status.php'
        };

        let data = {
            userIds: users.map(u => u.id),
            status: action === 'set_active' ? 1 : 0
        };

        $.post(urlMap[action], data, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                if (action === 'delete') {
                    result.users.forEach(function(userId) {
                        $(`tr[data-id="${userId}"]`).remove();
                    });
                } else if (action === 'set_active' || action === 'set_not_active') {
                    result.users.forEach(function(user) {
                        $(`tr[data-id="${user.id}"]`).replaceWith(generateUserRowHtml(user));
                    });
                }
                $('.userCheckbox, #selectAll').prop('checked', false);
            } else {
                alert(result.error.message);
            }
        });
    }

    // Handles the click event for the "Apply Action" buttons.
    $('#applyActionBtn, #applyActionBtnBottom').on('click', function () {
        const action = $(this).attr('id') === 'applyActionBtn' ? $('#userActions').val() : $('#userActionsBottom').val();
        const users = [];

        $('.userCheckbox:checked').each(function () {
            const userId = $(this).closest('tr').data('id');
            const userName = $(this).closest('tr').find('td:nth-child(2)').text();
            users.push({ id: userId, name: userName });
        });

        if (users.length === 0) {
            showCustomWarning('No users selected.');
            return;
        }

        if (action === '-Please Select-') {
            showCustomWarning('No action selected.');
            return;
        }

        if (action === 'delete') {
            const $userListToDelete = $('#userListToDelete');
            $userListToDelete.empty();
            users.forEach(user => $userListToDelete.append(`<li>${user.name}</li>`));
            $('#deleteConfirmModal').modal('show');

            $('#confirmDeleteBtn').off('click').on('click', function () {
                applyUserAction('delete', users);
                $('#deleteConfirmModal').modal('hide');
            });
        } else {
            applyUserAction(action, users);
        }
    });

    // Handles the click event for the "Delete User" buttons.
    $(document).on('click', '.deleteUserBtn', function () {
        const userId = $(this).data('id');
        const userName = $(this).closest('tr').find('td:nth-child(2)').text();
        const $userListToDelete = $('#userListToDelete');
        $userListToDelete.empty().append(`<li>${userName}</li>`);
        $('#deleteConfirmModal').modal('show');

        $('#confirmDeleteBtn').data('userId', userId);
    });

    // Handles the delete confirmation and delete the selected users.
    $('#confirmDeleteBtn').off('click').on('click', function () {
        const userId = $(this).data('userId');
        applyUserAction('delete', [{ id: userId }]);
        $('#deleteConfirmModal').modal('hide');
    });

    // Toggles the selection of all user checkboxes based on the "Select All" checkbox state.
    $(document).on('change', '#selectAll', function () {
        $('.userCheckbox').prop('checked', $(this).is(':checked'));
    });

    // Updates the "Select All" checkbox state based on whether all individual user checkboxes are checked.
    $(document).on('change', '.userCheckbox', function () {
        const allChecked = $('.userCheckbox:checked').length === $('.userCheckbox').length;
        $('#selectAll').prop('checked', allChecked);
    });

});
