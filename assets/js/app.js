$(document).ready(function() {
    // Constants
    const ACTIONS = {
        DELETE: 'delete',
        SET_ACTIVE: 'set_active',
        SET_NOT_ACTIVE: 'set_not_active'
    };

    const API_ENDPOINTS = {
        GET_USER: 'index.php?action=getUser',
        ADD_USER: 'index.php?action=addUser',
        EDIT_USER: 'index.php?action=editUser',
        DELETE_USERS: 'index.php?action=deleteUsers',
        SET_STATUS: 'index.php?action=setStatus'
    };

    const ERROR_MESSAGES = {
        USER_NOT_FOUND: 'User not found in the database.',
        MULTIPLE_USERS_NOT_FOUND: 'One or more selected users were not found in the database.',
        GENERAL_ERROR: 'An error occurred while processing your request.'
    };

    // Modal handling functions
    function showModal(modalId, message = '') {
        const $modal = $(`#${modalId}`);
        if (message) $modal.find('.modal-body').html(message);
        if (modalId === 'userModal') resetUserForm();
        $modal.modal('show');
    }

    function resetUserForm() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userModalLabel').text('Add User');
        $('#submitBtn').text('Save');
        $('#statusSwitch').prop('checked', false);
        $('#role_id').val(0);
        $('#role_id option[value=0]').show();
        $('#userModalError').hide().text('');
    }

    function resetCheckboxes() {
        $('.userCheckbox, #selectAll').prop('checked', false);
    }

    function closeUserModal() {
        $('#userModal').modal('hide');
        $('#userForm')[0].reset();
        $('#firstNameError, #lastNameError, #roleError').hide();
    }

    // API Handlers
    async function handleApiRequest(url, method = 'POST', data = null) {
        try {
            const response = await $.ajax({
                url: url,
                method: method,
                data: data,
                dataType: 'json'
            });
            if (!response || response.error !== null || !response.success) {
                throw new Error(response?.error || ERROR_MESSAGES.GENERAL_ERROR);
            }

            return response;
        } catch (error) {
            throw new Error(error.responseJSON?.error || ERROR_MESSAGES.GENERAL_ERROR);
        }
    }

    // User Modal Functions
    class UserModalHandler {
        static async handleAddUser() {
            resetUserForm();
            await showModal('userModal');
        }

        static async handleEditUser(userId) {
            try {
                const result = await handleApiRequest(API_ENDPOINTS.GET_USER, 'GET', { id: userId });

                if (!result.success || !result.user) {
                    throw new Error(ERROR_MESSAGES.USER_NOT_FOUND);
                }

                await showModal('userModal');
                UserModalHandler.populateEditForm(result.user);

            } catch (error) {
                await showModal('customErrorModal', ERROR_MESSAGES.USER_NOT_FOUND);
                return false;
            }
        }

        static populateEditForm(user) {
            $('#userId').val(user.id);
            $('#userModalLabel').text('Edit User');
            $('#submitBtn').text('Update');
            $('#firstName').val(user.name_first);
            $('#lastName').val(user.name_last);
            $('#statusSwitch').prop('checked', user.status === 1);
            $('#role_id').val(user.role_id);
            $('#role_id option[value=0]').hide();
            $('#userModalError').hide().text('');
        }
    }

    // User Actions Handler
    class UserActionsHandler {
        static async handleDeleteUsers(users) {
            try {
                const result = await handleApiRequest(API_ENDPOINTS.DELETE_USERS, 'POST', {
                    userIds: users.map(u => u.id)
                });

                if (!result.users || result.users.length === 0) {
                    throw new Error(ERROR_MESSAGES.MULTIPLE_USERS_NOT_FOUND);
                }

                result.users.forEach(userId => {
                    $(`tr[data-id="${userId}"]`).remove();
                });
                resetCheckboxes();
                return true;
            } catch (error) {
                showModal('customErrorModal', ERROR_MESSAGES.GENERAL_ERROR);
                return false;
            }
        }

        static async handleSetStatus(users, status) {
            try {
                const result = await handleApiRequest(API_ENDPOINTS.SET_STATUS, 'POST', {
                    userIds: users.map(u => u.id),
                    status: status
                });
                if (!result.users || result.users.length === 0) {
                    throw new Error(ERROR_MESSAGES.MULTIPLE_USERS_NOT_FOUND);
                }

                result.users.forEach(user => {
                    $(`tr[data-id="${user.id}"]`).replaceWith(generateUserRowHtml(user));
                });

                resetCheckboxes();
                return true;
            } catch (error) {
                showModal('customErrorModal', ERROR_MESSAGES.MULTIPLE_USERS_NOT_FOUND);
                return false;
            }
        }
    }

    // Form Handler
    class UserFormHandler {
        static validateForm() {
            const errors = {
                firstName: !$('#firstName').val().trim(),
                lastName: !$('#lastName').val().trim(),
                role: $('#role_id').val() === "0"
            };

            Object.keys(errors).forEach(field => {
                const $errorElement = $(`#${field}Error`);
                if (errors[field]) {
                    $errorElement.removeClass('error-message');
                } else {
                    $errorElement.addClass('error-message');
                }
            });

            return !Object.values(errors).some(Boolean);
        }

        static async handleAddSubmit(formData) {
            try {
                const result = await handleApiRequest(API_ENDPOINTS.ADD_USER, 'POST', formData);
                closeUserModal();
                $('#userTableBody').append(generateUserRowHtml(result.user));
                return true;

            } catch (error) {
                showModal('customErrorModal', ERROR_MESSAGES.GENERAL_ERROR);
                return false;
            }
        }

        static async handleEditSubmit(userId, formData) {
            try {
                const result = await handleApiRequest(API_ENDPOINTS.EDIT_USER, 'POST', formData);
                closeUserModal();
                $(`tr[data-id="${userId}"]`).replaceWith(generateUserRowHtml(result.user));
                return true;

            } catch (error) {
                showModal('customErrorModal', error.message || ERROR_MESSAGES.GENERAL_ERROR);
                return false;
            }
        }
    }

    $('.addUserBtn').on('click', function (e) {
        e.preventDefault();
        UserModalHandler.handleAddUser();
    });

    $(document).on('click', '.editUserBtn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const userId = $(this).data('id');
        UserModalHandler.handleEditUser(userId);
    });

    $(document).on('click', '.deleteUserBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $row = $(this).closest('tr');
        const userId = $row.data('id');
        const userName = $row.find('td:nth-child(2)').text();

        const $userList = $('#userListToDelete').empty();
        $userList.append(`<li>${userName}</li>`);

        $('#confirmDeleteBtn')
            .off('click')
            .on('click', function() {
                const success = UserActionsHandler.handleDeleteUsers([{id: userId, name: userName}]);
                if (success) {
                    $('#deleteConfirmModal').modal('hide');
                }
            });

        showModal('deleteConfirmModal');
    });

    $('#userForm').on('submit', function(e) {
        e.preventDefault();

        if (!UserFormHandler.validateForm()) {
            return;
        }

        const userId = $('#userId').val();
        const formData = $(this).serialize() +
            `&status=${$('#statusSwitch').is(':checked') ? 1 : 0}`;

        if (userId) {
            UserFormHandler.handleEditSubmit(userId, formData);
        } else {
            UserFormHandler.handleAddSubmit(formData);
        }
        resetCheckboxes();
    });

    $('#applyActionBtn, #applyActionBtnBottom').on('click', function() {
        const actionSelect = $(this).attr('id') === 'applyActionBtn' ? '#userActions' : '#userActionsBottom';
        const action = $(actionSelect).val();

        const users = $('.userCheckbox:checked').map(function() {
            const $row = $(this).closest('tr');
            return {
                id: $row.data('id'),
                name: $row.find('td:nth-child(2)').text()
            };
        }).get();

        if (users.length === 0) {
            return showModal('customWarningModal', 'No users selected.');
        }

        if (action === '-Please Select-') {
            return showModal('customWarningModal', 'No action selected.');
        }

        if (action === ACTIONS.DELETE) {
            const $userList = $('#userListToDelete').empty();
            users.forEach(user => $userList.append(`<li>${user.name}</li>`));

            $('#confirmDeleteBtn')
                .off('click')
                .on('click', function() {
                    const success = UserActionsHandler.handleDeleteUsers(users);
                    if (success) {
                        $('#deleteConfirmModal').modal('hide');
                        resetCheckboxes();
                    }
                });

            showModal('deleteConfirmModal');
        } else {
            const success = UserActionsHandler.handleSetStatus(
                users,
                action === ACTIONS.SET_ACTIVE ? 1 : 0
            );
            if (success) {
                resetCheckboxes();
            }
        }
    });

    $('#selectAll').on('change', function() {
        $('.userCheckbox').prop('checked', $(this).is(':checked'));
    });

    $(document).on('change', '.userCheckbox', function() {
        const allChecked = $('.userCheckbox:checked').length === $('.userCheckbox').length;
        $('#selectAll').prop('checked', allChecked);
    });
});