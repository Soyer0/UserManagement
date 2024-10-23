<?php
require __DIR__ . '/../includes/db.php';

$users = $_POST['userIds'] ?? [];

if (count($users) === 0) {
    echo json_encode(['status' => false, 'error' => ['code' => 400, 'message' => 'No users selected for deletion']]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($users), '?'));
$stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");

if ($stmt->execute($users)) {
    echo json_encode(['status' => true, 'userIds' => $users]);
} else {
    echo json_encode(['status' => false, 'error' => ['code' => 500, 'message' => 'Failed to delete user(s)']]);
}
exit;
