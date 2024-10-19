$(document).ready(function () {
    function loadUsers() {
        location.reload();
    }

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

    $('#addUserBtn, #addUserBtnBottom').on('click', function () {
        openUserModal('add');
    });

    $('.editUserBtn').on('click', function () {
        const userId = $(this).data('id');
        $.post('ajax/ajax.php', { action: 'get_user', id: userId }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                openUserModal('edit', result.user);
            } else {
                alert(result.error.message);
            }
        });
    });

    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        const userId = $('#userId').val();
        const action = userId ? 'update' : 'add';

        const selectedRole = $('#role').val();
        if (!selectedRole) {
            alert('Please select a role.');
            return;
        }
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

    function applyUserAction(action, users) {
        $.post('ajax/ajax.php', { action: action, users: users.map(u => u.id) }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                loadUsers();
            } else {
                alert(result.error.message);
            }
        });
    }

    $('#applyActionBtn, #applyActionBtnBottom').on('click', function () {
        const action = $(this).attr('id') === 'applyActionBtn' ? $('#userActions').val() : $('#userActionsBottom').val();
        const users = [];

        $('.userCheckbox:checked').each(function () {
            const userId = $(this).closest('tr').data('id');
            const userName = $(this).closest('tr').find('td:nth-child(2)').text();
            users.push({ id: userId, name: userName });
        });

        if (users.length === 0) {
            $('#noUsersSelectedModal').modal('show');
            return;
        }

        if (action === '-Please Select-') {
            $('#noActionSelectedModal').modal('show');
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

    $('.deleteUserBtn').on('click', function () {
        const userId = $(this).data('id');
        const userName = $(this).closest('tr').find('td:nth-child(2)').text();
        const $userListToDelete = $('#userListToDelete');
        $userListToDelete.empty().append(`<li>${userName}</li>`);
        $('#deleteConfirmModal').modal('show');

        $('#confirmDeleteBtn').data('userId', userId);
    });

    $('#confirmDeleteBtn').off('click').on('click', function () {
        const userId = $(this).data('userId');
        applyUserAction('delete', [{ id: userId }]);
        $('#deleteConfirmModal').modal('hide');
    });

    $('#selectAll').on('change', function () {
        $('.userCheckbox').prop('checked', $(this).is(':checked'));
    });

    $('.userCheckbox').on('change', function () {
        const allChecked = $('.userCheckbox:checked').length === $('.userCheckbox').length;
        $('#selectAll').prop('checked', allChecked);
    });
});
