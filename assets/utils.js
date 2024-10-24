window.generateUserRowHtml = function (user) {
    const statusClass = user.status == 1 ? 'active' : 'not-active';
    const roleNames = {
        1: 'Admin',
        2: 'User',
    };
    const roleName = roleNames[user.role_id] || 'Unknown Role';

    return `
        <tr data-id="${user.id}">
            <td><input type="checkbox" class="userCheckbox" value="${user.id}"></td>
            <td>${htmlspecials(user.name_first + ' ' + user.name_last)}</td>
            <td class="status"><span class="status-circle ${statusClass}"></span></td>
            <td>${roleName}</td>
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
}

// Вспомогательная функция для экранирования HTML символов
function htmlspecials(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
