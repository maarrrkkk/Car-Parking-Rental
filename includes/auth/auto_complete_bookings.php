<?php
// Auto-complete bookings that have passed their end time
// This script should be called via cron job every 5-10 minutes

require_once "../../config/database.php";

header('Content-Type: application/json');

try {
    $pdo->beginTransaction();
    
    // Find active bookings where end_time has passed
    $stmt = $pdo->prepare("
        SELECT b.id, b.slot_id, b.end_time, b.status
        FROM bookings b 
        WHERE b.status = 'active' 
        AND b.end_time IS NOT NULL 
        AND b.end_time <= NOW()
    ");
    $stmt->execute();
    $expiredBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updatedCount = 0;
    
    foreach ($expiredBookings as $booking) {
        // Update booking status to completed
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'completed' WHERE id = :id");
        $stmt->execute(['id' => $booking['id']]);
        
        // Make the slot available again
        $stmt = $pdo->prepare("UPDATE slots SET available = 1 WHERE id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
        
        // Clean up waitlist entries for this slot
        $stmt = $pdo->prepare("DELETE FROM waitlist WHERE slot_id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
        
        $updatedCount++;
    }
    
    $pdo->commit();
    
    echo json_encode([
        "success" => true, 
        "message" => "Auto-completed {$updatedCount} expired bookings",
        "updated_count" => $updatedCount
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>