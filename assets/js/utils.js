window.generateUserRowHtml = function (user) {
    let rowHtml = `<tr data-id="${user.id}">
        <td><input type="checkbox" class="userCheckbox" value="${user.id}"></td>`;

    userColumns.forEach(col => {
        if (col === 'status') {
            rowHtml += `<td class="status"><span class="status-circle ${user.status_name}"></span></td>`;
        } else if (col === 'role_id') {
            rowHtml += `<td>${user.role_name}</td>`;
        } else {
            rowHtml += `<td>${htmlspecials(user[col])}</td>`;
        }
    });

    rowHtml += `
        <td>
            <button class="btn btn-warning btn-sm editUserBtn" data-id="${user.id}">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-danger btn-sm deleteUserBtn" data-id="${user.id}">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;

    return rowHtml;
};


function htmlspecials(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
