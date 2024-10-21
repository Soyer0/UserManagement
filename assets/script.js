$(document).ready(function () {
    loadUsers();
    function loadUsers() {
        $.post('ajax/ajax.php', { action: 'get_all' }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                const users = result.user;
                $('#userTableBody').empty();

                users.forEach(user => {
                    addUserRow(user);
                });
            } else {
                alert(result.error.message);
            }
        });
    }

// A function for adding a new user row to the table.
    function addUserRow(user) {
        const rowHtml = `
        <tr data-id="${user.id}">
            <td>
                <input type="checkbox" class="userCheckbox" value="${user.id}">
            </td>
            <td>${htmlspecialchars(user.name_first + ' ' + user.name_last)}</td>
            <td class="status">
                <span class="status-circle ${user.status ? 'active' : 'not-active'}"></span>
            </td>
            <td>${htmlspecialchars(user.role)}</td>
            <td>
                <button class="btn btn-warning btn-sm editUserBtn" data-toggle="modal" data-target="#userModal" data-id="${user.id}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm deleteUserBtn" data-id="${user.id}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
        $('#userTableBody').append(rowHtml);
    }

// The htmlspecialchars function for escaping special characters.
    function htmlspecialchars(string) {
        return string.replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

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
            $('#role').val(user.role);

            if($('#userModalLabel').text() === 'Edit User') $('#role option[value=""]').hide();
            else $('#role option[value=""]').show();
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
        $.post('ajax/ajax.php', { action: 'get', id: userId }, function (response) {
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
        const selectedRole = $('#role').val();

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
    $('#userForm').on('submit', function (e) {
        e.preventDefault();

        if (!validateUserForm()) {
            return;
        }

        const userId = $('#userId').val();
        const action = userId ? 'update' : 'add';
        const status = $('#statusSwitch').is(':checked') ? 1 : 0;

        $.post('ajax/ajax.php', $(this).serialize() + `&action=${action}&status=${status}`, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                $('#userModal').modal('hide');
                loadUsers();
            } else {
                alert(result.error.message);
            }
        });
    });

    // Sends a POST request to apply a specified action (e.g., set active, delete) on selected users.
    function applyUserAction(action, users) {
        $.post('ajax/ajax.php', { action: action, users: users.map(u => u.id) }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                loadUsers();
                $('#selectAll').prop('checked', false);
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
