$(document).ready(function () {
    function loadUsers() {
        location.reload();
    }

    $('#addUserBtn').on('click', function () {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userModalLabel').text('Add User');
    });

    $('.editUserBtn').on('click', function () {
        const userId = $(this).data('id');
        $.post('ajax/ajax.php', {action: 'get_user', id: userId}, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                const user = result.user;
                $('#firstName').val(user.name_first);
                $('#lastName').val(user.name_last);
                $('#status').val(user.status);
                $('#role').val(user.role);
                $('#userId').val(user.id);
                $('#userModalLabel').text('Edit User');
            } else {
                alert(result.error.message);
            }
        });
    });

    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        const userId = $('#userId').val();
        const action = userId ? 'update' : 'add';

        $.post('ajax/ajax.php', $(this).serialize() + `&action=${action}`, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                $('#userModal').modal('hide');
                loadUsers();
            } else {
                alert(result.error.message);
            }
        });
    });

    $('#applyActionBtn, #applyActionBtnBottom').on('click', function () {
        const action = $(this).attr('id') === 'applyActionBtn' ? $('#userActions').val() : $('#userActionsBottom').val();
        const users = [];
        $('.userCheckbox:checked').each(function () {
            users.push($(this).closest('tr').data('id'));
        });

        if (users.length === 0) {
            alert('No users selected');
            return;
        }

        if (action === 'Please Select') {
            alert('Please select an action');
            return;
        }

        $.post('ajax/ajax.php', {action: action, users: users}, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                loadUsers();
            } else {
                alert(result.error.message);
            }
        });
    });

    $('.deleteUserBtn').on('click', function () {
        const userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.post('ajax/ajax.php', {action: 'delete_user', id: userId}, function (response) {
                const result = JSON.parse(response);
                if (result.status) {
                    loadUsers();
                } else {
                    alert(result.error.message);
                }
            });
        }
    });

    $('#selectAll').on('change', function () {
        $('.userCheckbox').prop('checked', this.checked);
    });
});