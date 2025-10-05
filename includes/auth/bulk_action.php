<?php
require_once "../../config/database.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action'], $data['ids']) || !is_array($data['ids'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$action = $data['action'];
$ids = array_map('intval', $data['ids']);
$in  = str_repeat('?,', count($ids) - 1) . '?';

try {
    if ($action === "Delete Users") {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($in)");
        $stmt->execute($ids);
        echo json_encode(["success" => true, "message" => "Users deleted"]);
    } elseif ($action === "Suspend Users") {
        $stmt = $pdo->prepare("UPDATE users SET status = 'Suspended' WHERE id IN ($in)");
        $stmt->execute($ids);
        echo json_encode(["success" => true, "message" => "Users suspended"]);
    } elseif ($action === "Activate Users") {
        $stmt = $pdo->prepare("UPDATE users SET status = 'Active' WHERE id IN ($in)");
        $stmt->execute($ids);
        echo json_encode(["success" => true, "message" => "Users activated"]);
    } elseif ($action === "Send Email") {
        // TODO: integrate PHPMailer for real emails
        echo json_encode(["success" => true, "message" => "Emails sent"]);
    } else {
        echo json_encode(["success" => false, "message" => "Unknown action"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
