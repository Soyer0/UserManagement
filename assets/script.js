$(document).ready(function () {
    let selectedUsers = [];

    function loadUsers() {
        // Вместо перезагрузки страницы можно динамически обновить список пользователей
        location.reload();
    }

    function deleteUsers(users) {
        $.post('ajax/ajax.php', { action: 'delete_users', users: users.map(u => u.id) }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                loadUsers();
            } else {
                alert(result.error.message);
            }
            $('#deleteConfirmModal').modal('hide');
        });
    }

    // Открытие формы для добавления пользователя
    $('#addUserBtn').on('click', function () {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userModalLabel').text('Add User');
        $('#submitBtn').text('Save');
        $('#userModal').modal('show');
    });

    // Открытие формы для редактирования пользователя
    $('.editUserBtn').on('click', function () {
        const userId = $(this).data('id');
        $.post('ajax/ajax.php', { action: 'get_user', id: userId }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                const user = result.user;
                $('#firstName').val(user.name_first);
                $('#lastName').val(user.name_last);
                $('#status').val(user.status);
                $('#role').val(user.role);
                $('#userId').val(user.id);
                $('#userModalLabel').text('Edit User');
                $('#submitBtn').text('Update');
                $('#userModal').modal('show');
            } else {
                alert(result.error.message);
            }
        });
    });

    // Отправка формы пользователя
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

    // Применение действий к выбранным пользователям
    $('#applyActionBtn, #applyActionBtnBottom').on('click', function () {
        const action = $(this).attr('id') === 'applyActionBtn' ? $('#userActions').val() : $('#userActionsBottom').val();
        const users = [];

        $('.userCheckbox:checked').each(function () {
            const userId = $(this).closest('tr').data('id');
            const userName = $(this).closest('tr').find('.userName').text(); // Извлечение имени пользователя
            users.push({ id: userId, name: userName });
        });

        if (users.length === 0) {
            $('#noUsersSelectedModal').modal('show'); // Модальное окно, если не выбраны пользователи
            return;
        }

        if (action === '-Please Select-') {
            $('#noActionSelectedModal').modal('show'); // Модальное окно, если не выбрано действие
            return;
        }

        if (action === 'delete') {
            $('#userListToDelete').empty(); // Очищаем список перед добавлением
            users.forEach(function (user) {
                $('#userListToDelete').append(`<li>${user.name}</li>`); // Добавляем имена пользователей
            });
            $('#deleteConfirmModal').modal('show'); // Показать модальное окно

            // Обработчик для подтверждения удаления
            $('#confirmDeleteBtn').off('click').on('click', function () {
                $.post('ajax/ajax.php', { action: 'delete_users', users: users.map(u => u.id) }, function (response) {
                    const result = JSON.parse(response);
                    if (result.status) {
                        loadUsers(); // Перезагружаем пользователей
                    } else {
                        alert(result.error.message);
                    }
                    $('#deleteConfirmModal').modal('hide'); // Закрываем модальное окно после выполнения запроса
                });
            });
        } else {
            $.post('ajax/ajax.php', { action: action, users: users.map(u => u.id) }, function (response) {
                const result = JSON.parse(response);
                if (result.status) {
                    loadUsers(); // Перезагружаем пользователей
                } else {
                    alert(result.error.message);
                }
            });
        }
    });

// Модальное окно для удаления одного пользователя
    $('.deleteUserBtn').on('click', function () {
        const userId = $(this).data('id');
        const userName = $(this).closest('tr').find('.userName').text(); // Извлечение имени пользователя
        selectedUsers = []; // Очистить массив выбранных пользователей
        selectedUsers.push({ id: userId, name: userName }); // Добавить одного пользователя для удаления

        // Заполнение списка имен в модальном окне
        $('#userListToDelete').empty();
        $('#userListToDelete').append(`<li>${userName}</li>`);

        $('#deleteConfirmModal').modal('show');
    });

    // Обработчик для подтверждения удаления
    $('#confirmDeleteBtn').off('click').on('click', function () {
        const userId = selectedUsers[0].id; // Получаем id пользователя из массива
        $.post('ajax/ajax.php', { action: 'delete_user', id: userId }, function (response) {
            const result = JSON.parse(response);
            if (result.status) {
                loadUsers();
            } else {
                alert(result.error.message);
            }
            $('#deleteConfirmModal').modal('hide'); // Закрываем модальное окно после выполнения запроса
        });
    });

    // Логика для работы с checkbox'ами
    $('#selectAll').on('change', function () {
        const isChecked = $(this).is(':checked');
        $('.userCheckbox').prop('checked', isChecked);
    });

    $('.userCheckbox').on('change', function () {
        const allChecked = $('.userCheckbox:checked').length === $('.userCheckbox').length;
        $('#selectAll').prop('checked', allChecked);
    });
});
