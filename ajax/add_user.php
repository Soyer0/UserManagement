<?php
require __DIR__ . '/../includes/db.php';

$firstName = trim($_POST['firstName']) ?? '';
$lastName = trim($_POST['lastName']) ?? '';
$status = $_POST['status'] ?? 0;
$role_id = $_POST['role_id'] ?? 0;

if($role_id === 0) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 400, 'message' => 'Role ID is required'],
    ]);
    exit;
}

if (empty($firstName) || empty($lastName)) {
    echo json_encode(['status' => false, 'error' => ['code' => 400, 'message' => 'First name and last name are required']]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO users (name_first, name_last, status, role_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $status, $role_id]);

    $newUserId = $pdo->lastInsertId();

    $user = [
        'id' => $newUserId,
        'name_first' => $firstName,
        'name_last' => $lastName,
        'status' => (bool)$status,
        'role_id' => $role_id
    ];

    echo json_encode(['status' => true, 'error' => null, 'user' => $user]);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'error' => ['code' => 500, 'message' => $e->getMessage()], 'user' => null]);
}
exit;
