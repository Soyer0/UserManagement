<?php
require __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? null;

if (empty($id)) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 400, 'message' => 'User ID is required'],
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'status' => true,
            'error' => null,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'error' => ['code' => 404, 'message' => 'User not found'],
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'error' => ['code' => 500, 'message' => $e->getMessage()],
    ]);
}
exit;