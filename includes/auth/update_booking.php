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
    $pdo->beginTransaction();
    
    // Get booking details first
    $stmt = $pdo->prepare("SELECT slot_id FROM bookings WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        echo json_encode(["success" => false, "message" => "Booking not found"]);
        exit;
    }
    
    // Update booking status
    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);
    
    // If booking is cancelled or completed, make the slot available again
    if (in_array($status, ['cancelled', 'completed'])) {
        $stmt = $pdo->prepare("UPDATE slots SET available = 1 WHERE id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
    }
    
    $pdo->commit();
    
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>