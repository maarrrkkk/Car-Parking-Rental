<?php
session_start();
require_once "../../config/database.php";

header('Content-Type: application/json');

// Check if user is authenticated and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

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
            // Get booking details before deletion for stats update
            $stmt = $pdo->prepare("SELECT user_id, amount, status FROM bookings WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($booking) {
                // Delete the booking
                $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
                $stmt->execute(['id' => $id]);
                
                // Update user statistics
                // Decrement total_bookings
                $stmt = $pdo->prepare("UPDATE users SET total_bookings = GREATEST(total_bookings - 1, 0) WHERE id = :user_id");
                $stmt->execute(['user_id' => $booking['user_id']]);
                
                // If booking was paid (active or completed), deduct the amount from total_spent
                if (in_array($booking['status'], ['active', 'completed'])) {
                    $stmt = $pdo->prepare("UPDATE users SET total_spent = GREATEST(total_spent - :amount, 0) WHERE id = :user_id");
                    $stmt->execute([
                        'amount' => $booking['amount'],
                        'user_id' => $booking['user_id']
                    ]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Booking not found"]);
                exit;
            }
            break;

        default:
            echo json_encode(["success" => false, "message" => "Unknown type"]);
            exit;
    }

    // Execute the statement only if it wasn't executed in the case block
    if ($type !== 'booking') {
        $stmt->execute(['id' => $id]);
    }

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
