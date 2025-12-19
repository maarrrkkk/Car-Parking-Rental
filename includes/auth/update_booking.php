<?php
session_start();
require_once "../../config/database.php";

header('Content-Type: application/json');

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

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
    $stmt = $pdo->prepare("
        SELECT b.*, s.available as slot_available 
        FROM bookings b 
        JOIN slots s ON b.slot_id = s.id 
        WHERE b.id = :id
    ");
    $stmt->execute(['id' => $id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        echo json_encode(["success" => false, "message" => "Booking not found"]);
        exit;
    }
    
    // Get the original status to check if it changed
    $originalStatus = $booking['status'];
    
    // Update booking status
    $stmt = $pdo->prepare("UPDATE bookings SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);
    
    // If booking is cancelled or completed, make the slot available again
    if (in_array($status, ['cancelled', 'completed'])) {
        // Update slot availability to available
        $stmt = $pdo->prepare("UPDATE slots SET available = 1 WHERE id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
        
        // Clean up any waitlist entries for this slot since it's now available
        $stmt = $pdo->prepare("DELETE FROM waitlist WHERE slot_id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
        
    } elseif ($status === 'active' && $originalStatus !== 'active') {
        // If booking is being activated, make sure slot is unavailable
        $stmt = $pdo->prepare("UPDATE slots SET available = 0 WHERE id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
    }
    
    $pdo->commit();
    
    // Log the action for audit purposes
    $actionMessage = "Booking ID {$id} status updated from '{$originalStatus}' to '{$status}'";
    if (in_array($status, ['cancelled', 'completed'])) {
        $actionMessage .= ". Slot automatically made available.";
    }
    
    echo json_encode(["success" => true, "message" => $actionMessage]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>