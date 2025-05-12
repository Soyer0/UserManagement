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
        SET_STATUS: 'index.php?action=setStatus',
        SEARCH_USERS: 'index.php?action=searchUsers',
    };

    const ERROR_MESSAGES = {
        USER_NOT_FOUND: 'User not found in the database.',
        MULTIPLE_USERS_NOT_FOUND: 'One or more selected users were not found in the database.',
        GENERAL_ERROR: 'An error occurred while processing your request.'
    };

    function generateDynamicFields() {
        const container = $('#dynamicFieldsContainer');
        container.empty();
        userColumns.forEach(column => {
            switch (column) {
                case 'status':
                    container.append(`
                        <div class="form-group mb-3">
                            <label for="statusSwitch" class="form-label">Status</label>
                            <div class="form-check form-switch form-switch-lg">
                                <input class="form-check-input" type="checkbox" id="statusSwitch" name="statusSwitch">
                                <label class="form-check-label" for="statusSwitch"></label>
                            </div>
                        </div>
                    `);
                    break;
                case 'role_id':
                    container.append(`
                        <div class="form-group mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select" id="role_id" name="role_id">
                                <option value="0">-Please select-</option>
                                <option value="1">Admin</option>
                                <option value="2">User</option>
                            </select>
                            <small class="form-text text-danger error-message" id="roleError">Please choose a role from the list.</small>
                        </div>
                    `);
                    break;
                default:
                    container.append(`
                        <div class="form-group mb-3">
                            <label for="${column}" class="form-label">${column.charAt(0).toUpperCase() + column.slice(1)}</label>
                            <input type="text" class="form-control" id="${column}" name="${column}">
                        </div>
                    `);
                    break;
            }
        });
    }

    // Modal handling functions
    function showModal(modalId, message = '') {
        const $modal = $(`#${modalId}`);
        if (message) $modal.find('.modal-body').html(message);
        if (modalId === 'userModal') {
            resetUserForm();
            generateDynamicFields();
        }
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
            if (!response || response.error || !response.success) {
                throw new Error(response?.error || ERROR_MESSAGES.GENERAL_ERROR);
            }

            return response;
        } catch (error) {
            const errorMessage = error.responseJSON?.error || error.message || ERROR_MESSAGES.GENERAL_ERROR;
            throw new Error(errorMessage);
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
            $('#name_first').val(user.name_first);
            $('#name_last').val(user.name_last);
            $('#statusSwitch').prop('checked', user.status_name === 'active');
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
                resetSearchFilter();
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

                resetSearchFilter();
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
                firstName: !$('#name_first').val().trim(),
                lastName: !$('#name_last').val().trim(),
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
                resetSearchFilter();
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
                resetSearchFilter();
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
        const nameFirst = $row.find('td:nth-child(2)').text();
        const nameLast = $row.find('td:nth-child(3)').text();
        const userName = `${nameFirst} ${nameLast}`;

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
                name: $row.find('td:nth-child(2)').text() + ' ' + $row.find('td:nth-child(3)').text()
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

    function resetSearchFilter() {
        $('#userSearchInput').val('');
        $('#userTableBody tr').show();
    }


    $('#searchBtn').on('click', async function() {
        const searchValue = $('#userSearchInput').val().trim();

        if (searchValue.length > 0) {
            try {
                const response = await handleApiRequest(API_ENDPOINTS.SEARCH_USERS, 'GET', { search: searchValue });
                if (response.success) {
                    const userTableBody = $('#userTableBody');
                    userTableBody.empty();
                    response.users.forEach(user => {
                        userTableBody.append(generateUserRowHtml(user));
                    });
                } else {
                    showModal('customWarningModal', 'No users found');
                }
            } catch (error) {
                showModal('customErrorModal', error.message || 'An error occurred while searching.');
            }
        } else {
            showModal('customWarningModal', 'Please enter a user name in search.');
        }
    });

});