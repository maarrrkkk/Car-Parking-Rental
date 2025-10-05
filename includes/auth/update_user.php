<?php
require_once "../../config/database.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$userId = (int)$data['id'];
$fields = [];
$params = [];

// Build SQL dynamically based on provided fields
$allowed = ['firstname','lastname','email','phone','status','total_bookings','total_spent'];

foreach ($allowed as $field) {
    if (isset($data[$field])) {
        $fields[] = "$field = :$field";
        $params[":$field"] = $data[$field];
    }
}

if (empty($fields)) {
    echo json_encode(["success" => false, "message" => "No fields to update"]);
    exit;
}

$params[":id"] = $userId;

try {
    $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(["success" => true, "message" => "User updated"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
