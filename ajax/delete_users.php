<?php
require __DIR__ . '/../includes/db.php';

$users = $_POST['userIds'] ?? [];

if (count($users) === 0) {
    echo json_encode(['status' => false, 'error' => ['code' => 400, 'message' => 'No users selected for deletion']]);
    exit;
}

try {
    $placeholders = implode(',', array_fill(0, count($users), '?'));
    $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");

    if ($stmt->execute($users)) {
        echo json_encode([
            'status' => true,
            'error' => null,
            'users' => $users
        ]);
    } else {
        throw new PDOException('Failed to delete user(s)');
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 500, 'message' => $e->getMessage()],
    ]);
}
exit;
