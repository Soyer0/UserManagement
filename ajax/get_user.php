<?php
require __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(['status' => true, 'user' => $user]);
} else {
    echo json_encode(['status' => false, 'error' => ['code' => 100, 'message' => 'User not found']]);
}
exit;
