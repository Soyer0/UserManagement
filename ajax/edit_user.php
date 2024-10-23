<?php
require __DIR__ . '/../includes/db.php';
require __DIR__. '/../includes/userHtmlRow.php';

$userId = $_POST['userId'] ?? null;
$firstName = trim($_POST['firstName']) ?? '';
$lastName = trim($_POST['lastName']) ?? '';
$status = $_POST['status'] ?? 0;
$role = $_POST['role'] ?? '';

try {
    $stmt = $pdo->prepare("UPDATE users SET name_first = ?, name_last = ?, status = ?, role = ? WHERE id = ?");
    $stmt->execute([$firstName, $lastName, $status, $role, $userId]);

    $html = generateUserRowHtml($userId, $firstName, $lastName, $status, $role);

    echo json_encode(['status' => true, 'html' => $html, 'userId' => $userId]);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'error' => ['code' => 500, 'message' => $e->getMessage()]]);
}
exit;
