<?php
function generateUserRowHtml($userId, $firstName, $lastName, $status, $role): string
{
    $statusClass = $status ? 'active' : 'not-active';

    return '<tr data-id="' . $userId . '">
                <td><input type="checkbox" class="userCheckbox" value="' . $userId . '"></td>
                <td>' . htmlspecialchars($firstName . ' ' . $lastName) . '</td>
                <td class="status"><span class="status-circle ' . $statusClass . '"></span></td>
                <td>' . htmlspecialchars($role) . '</td>
                <td>
                    <button class="btn btn-warning btn-sm editUserBtn" data-toggle="modal" data-target="#userModal" data-id="' . $userId . '">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-danger btn-sm deleteUserBtn" data-id="' . $userId . '">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>';
}
