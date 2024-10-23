<?php
require __DIR__ . '/../includes/db.php';

$userIds = $_POST['userIds'] ?? null;
$status = $_POST['status'] ?? null;

if (empty($userIds) || $status === null) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 400, 'message' => 'User IDs and status are required'],
        'users' => null
    ]);
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

        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT id, name_first, name_last, status, role_id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $updatedUsers[] = $user;
            }
        }
    }

    echo json_encode([
        'status' => true,
        'error' => null,
        'users' => $updatedUsers
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 500, 'message' => $e->getMessage()],
    ]);
}
exit;