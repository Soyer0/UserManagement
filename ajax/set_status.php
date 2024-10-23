<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/userHtmlRow.php';

$userIds = $_POST['userIds'] ?? null;
$status = $_POST['status'] ?? null;


if (empty($userIds) || $status === null) {
    echo json_encode(['status' => false, 'error' => ['code' => 400, 'message' => 'User IDs and status are required']]);
    exit;
}

if (!is_array($userIds)) {
    $userIds = [$userIds];
}

try {
    $updatedUsers = [];
    foreach ($userIds as $userId) {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $userId]);

        $stmt = $pdo->prepare("SELECT name_first, name_last, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $html = generateUserRowHtml($userId, $user['name_first'], $user['name_last'], $status, $user['role']);
            $updatedUsers[] = ['id' => $userId, 'html' => $html];
        }
        else {
            echo json_encode(['status' => false, 'error' => ['code' => 100, 'message' => 'User not found']]);
            exit;
        }
    }

    echo json_encode(['status' => true, 'users' => $updatedUsers]);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'error' => ['code' => 500, 'message' => $e->getMessage()]]);
}
exit;