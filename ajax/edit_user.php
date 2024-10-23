<?php
require __DIR__ . '/../includes/db.php';

$userId = $_POST['userId'] ?? null;
$firstName = trim($_POST['firstName']) ?? '';
$lastName = trim($_POST['lastName']) ?? '';
$status = $_POST['status'] ?? 0;
$role = $_POST['role'] ?? '';

if (empty($userId)) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 400, 'message' => 'User ID, first name, and last name are required'],
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET name_first = ?, name_last = ?, status = ?, role = ? WHERE id = ?");
    $stmt->execute([$firstName, $lastName, $status, $role, $userId]);

    $user = [
        'id' => $userId,
        'name_first' => $firstName,
        'name_last' => $lastName,
        'status' => (bool)$status,
        'role' => $role
    ];

    echo json_encode([
        'status' => true,
        'error' => null,
        'user' => $user
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 500, 'message' => $e->getMessage()],
    ]);
}
exit;
