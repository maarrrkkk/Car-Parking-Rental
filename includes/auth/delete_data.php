<?php
require_once "../../config/database.php";

header('Content-Type: application/json');

// Get raw input (since you're sending JSON in fetch)
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['type'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$id = intval($data['id']);
$type = $data['type'];

try {
    switch ($type) {
        case "user":
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            break;

        case "slot":
            $stmt = $pdo->prepare("DELETE FROM slots WHERE id = :id");
            break;

        case "booking":
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
            break;

        case "payment":
            $stmt = $pdo->prepare("DELETE FROM payments WHERE id = :id");
            break;

        default:
            echo json_encode(["success" => false, "message" => "Unknown type"]);
            exit;
    }

    $stmt->execute(['id' => $id]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
