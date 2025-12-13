<?php
require_once "../../config/database.php";

// Function to recalculate user statistics
function recalculateUserStats($pdo) {
    try {
        // Get all users
        $stmt = $pdo->query("SELECT id FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($users as $userId) {
            // Count total bookings for this user
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $totalBookings = $stmt->fetchColumn();
            
            // Calculate total spent (only for completed bookings with payments)
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM bookings WHERE user_id = :user_id AND status IN ('active', 'completed') AND paid_at IS NOT NULL");
            $stmt->execute(['user_id' => $userId]);
            $totalSpent = $stmt->fetchColumn();
            
            // Update user statistics
            $stmt = $pdo->prepare("UPDATE users SET total_bookings = :bookings, total_spent = :spent WHERE id = :user_id");
            $stmt->execute([
                'bookings' => $totalBookings,
                'spent' => $totalSpent,
                'user_id' => $userId
            ]);
            
            echo "Updated user {$userId}: {$totalBookings} bookings, ₱{$totalSpent} total spent\n";
        }
        
        echo "User statistics recalculation completed successfully!\n";
        
    } catch (Exception $e) {
        echo "Error recalculating statistics: " . $e->getMessage() . "\n";
    }
}

// Run the recalculation
recalculateUserStats($pdo);
?>