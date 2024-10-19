<?php

require __DIR__ . '/../includes/db.php';

$action = isset($_POST['action']) ? $_POST['action'] : null;

function respond($status, $error = null, $user = null)
{
    echo json_encode(['status' => $status, 'error' => $error, 'user' => $user]);
    exit;
}

if ($action === 'get_user') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        respond(true, null, $user);
    } else {
        respond(false, ['code' => 100, 'message' => 'User not found']);
    }
}

if ($action === 'delete') {
    $users = isset($_POST['users']) ? $_POST['users'] : [];

    if (count($users) === 0) {
        respond(false, ['code' => 400, 'message' => 'No users selected for deletion']);
    }

    $placeholders = implode(',', array_fill(0, count($users), '?'));
    $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");

    if ($stmt->execute($users)) {
        respond(true);
    } else {
        respond(false, ['code' => 500, 'message' => 'Failed to delete user(s)']);
    }
}

if (in_array($action, ['add', 'update'])) {
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $userId = isset($_POST['userId']) ? $_POST['userId'] : null;

    if (empty($firstName) || empty($lastName)) {
        respond(false, ['code' => 400, 'message' => 'First name and last name are required']);
    }

    try {
        if ($action === 'update' && $userId) {
            $stmt = $pdo->prepare("UPDATE users SET name_first = ?, name_last = ?, status = ?, role = ? WHERE id = ?");
            $stmt->execute([$firstName, $lastName, $status, $role, $userId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name_first, name_last, status, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $status, $role]);
        }
        respond(true);
    } catch (PDOException $e) {
        respond(false, ['code' => 500, 'message' => $e->getMessage()]);
    }
}

if (in_array($action, ['set_active', 'set_not_active'])) {
    $users = isset($_POST['users']) ? $_POST['users'] : [];
    $status = $action === 'set_active' ? 1 : 0;
    $placeholders = implode(',', array_fill(0, count($users), '?'));

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id IN ($placeholders)");
    $stmt->execute(array_merge([$status], $users));
    respond(true);
}
