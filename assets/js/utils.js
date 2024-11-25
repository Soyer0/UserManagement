window.generateUserRowHtml = function (user) {
    return `
        <tr data-id="${user.id}">
            <td><input type="checkbox" class="userCheckbox" value="${user.id}"></td>
            <td>${htmlspecials(user.name_first + ' ' + user.name_last)}</td>
            <td class="status"><span class="status-circle ${user.status == 1 ? 'active' : ''}"></span></td>
            <td>${window.rolesMap[parseInt(user.role_id, 10)] || 'Unknown'}</td>
            <td>
                <button class="btn btn-warning btn-sm editUserBtn" data-id="${user.id}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm deleteUserBtn" data-id="${user.id}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

function htmlspecials(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
