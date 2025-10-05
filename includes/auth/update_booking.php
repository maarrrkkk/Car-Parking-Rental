<?php
require_once "../../config/database.php";

header('Content-Type: application/json');

// Get raw input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$id = intval($data['id']);
$status = $data['status'];

$validStatuses = ['pending', 'active', 'completed', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(["success" => false, "message" => "Invalid status"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>