<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Handle user-initiated booking cancellation
if ($action === 'cancel') {
    $bookingId = $_POST['booking_id'] ?? null;
    
    if (!$bookingId) {
        echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Verify booking belongs to user and get booking details
        $stmt = $pdo->prepare("
            SELECT b.*, s.available as slot_available 
            FROM bookings b 
            JOIN slots s ON b.slot_id = s.id 
            WHERE b.id = :booking_id AND b.user_id = :user_id
        ");
        $stmt->execute(['booking_id' => $bookingId, 'user_id' => $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit;
        }
        
        // Check if booking can be cancelled (not active)
        if ($booking['status'] === 'active') {
            echo json_encode(['success' => false, 'message' => 'Cannot cancel an active booking']);
            exit;
        }
        
        // Update booking status to cancelled
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :booking_id");
        $stmt->execute(['booking_id' => $bookingId]);
        
        // Clean up waitlist entries for this slot (in case there are any)
        $stmt = $pdo->prepare("DELETE FROM waitlist WHERE slot_id = :slot_id");
        $stmt->execute(['slot_id' => $booking['slot_id']]);
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Failed to cancel booking: ' . $e->getMessage()]);
    }
    exit;
}

$slotId = $_POST['slot_id'] ?? null;
$startTime = $_POST['start_time'] ?? null;
$endTime = $_POST['end_time'] ?? null;
$durationType = $_POST['duration_type'] ?? null;
$vehicleType = $_POST['vehicle_type'] ?? null;
$confirmPayment = isset($_POST['confirm_payment']) && $_POST['confirm_payment'] === '1';
$bookingId = $_POST['booking_id'] ?? null;

if ($confirmPayment) {
    // Handle payment confirmation
    if (!$bookingId || !isset($_FILES['receipt'])) {
        echo json_encode(['success' => false, 'message' => 'Missing booking ID or receipt']);
        exit;
    }

    // Handle receipt upload
    $receiptFile = $_FILES['receipt'];
    if ($receiptFile['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Receipt upload failed']);
        exit;
    }

    // Validate file type
    $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    if (!in_array($receiptFile['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid receipt file type']);
        exit;
    }

    // Create directory if not exists
    $uploadDir = '../assets/images/receipts/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($receiptFile['name'], PATHINFO_EXTENSION);
    $filename = 'receipt_' . $bookingId . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (!move_uploaded_file($receiptFile['tmp_name'], $filepath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save receipt']);
        exit;
    }

    // Update booking with receipt and status
    try {
        $pdo->beginTransaction();
        
        // Get booking amount for updating user stats
        $stmt = $pdo->prepare("SELECT amount, user_id FROM bookings WHERE id = :id");
        $stmt->execute(['id' => $bookingId]);
        $bookingData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update booking status to active when receipt is confirmed
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'active', receipt = :receipt, paid_at = NOW() WHERE id = :id");
        $stmt->execute([
            'receipt' => 'assets/images/receipts/' . $filename,
            'id' => $bookingId
        ]);
        
        // Update user total_spent when payment is confirmed
        if ($bookingData) {
            $stmt = $pdo->prepare("UPDATE users SET total_spent = total_spent + :amount WHERE id = :user_id");
            $stmt->execute([
                'amount' => $bookingData['amount'],
                'user_id' => $bookingData['user_id']
            ]);
        }
        
        // Mark slot as unavailable when payment is confirmed (booking becomes active)
        $stmt = $pdo->prepare("UPDATE slots SET available = 0 WHERE id = (SELECT slot_id FROM bookings WHERE id = :id)");
        $stmt->execute(['id' => $bookingId]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Payment confirmed successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        
        // On failure, slot remains available (was never marked unavailable)
        echo json_encode(['success' => false, 'message' => 'Payment confirmation failed: ' . $e->getMessage()]);
    }
    exit;
}

if (!$slotId || !$startTime || !$endTime || !$durationType || !$vehicleType) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check for time conflicts with existing active bookings
$stmt = $pdo->prepare("
    SELECT id, user_id, start_time, end_time
    FROM bookings
    WHERE slot_id = :slot_id
    AND status = 'active'
    AND (
        (start_time <= :start_time AND end_time > :start_time) OR
        (start_time < :end_time AND end_time >= :end_time) OR
        (start_time >= :start_time AND end_time <= :end_time)
    )
");
$stmt->execute([
    'slot_id' => $slotId,
    'start_time' => $startTime,
    'end_time' => $endTime
]);
$conflictingBooking = $stmt->fetch(PDO::FETCH_ASSOC);

if ($conflictingBooking) {
    // Check if current user already has this booking
    if ($conflictingBooking['user_id'] == $userId) {
        echo json_encode(['success' => false, 'message' => 'You already have an active booking for this time slot.']);
        exit;
    }
    
    // Check if already in waitlist
    $stmt = $pdo->prepare("SELECT id FROM waitlist WHERE user_id = :user_id AND slot_id = :slot_id");
    $stmt->execute(['user_id' => $userId, 'slot_id' => $slotId]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'You are already in the waitlist for this slot.']);
        exit;
    }

    // Add to waitlist
    $stmt = $pdo->prepare("INSERT INTO waitlist (user_id, slot_id) VALUES (:user_id, :slot_id)");
    $stmt->execute(['user_id' => $userId, 'slot_id' => $slotId]);
    
    $currentUserEndTime = date('M d, Y h:i A', strtotime($conflictingBooking['end_time']));
    echo json_encode(['success' => true, 'message' => "Slot is currently unavailable until {$currentUserEndTime}. You have been added to the waitlist."]);
    exit;
}

// Validate slot exists (even if available = 0, slot should still exist)
$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = :id");
$stmt->execute(['id' => $slotId]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    echo json_encode(['success' => false, 'message' => 'Slot not found']);
    exit;
}

// Check if slot is available for booking (not marked as unavailable)
if ($slot['available'] == 0) {
    // Check if already in waitlist
    $stmt = $pdo->prepare("SELECT id FROM waitlist WHERE user_id = :user_id AND slot_id = :slot_id");
    $stmt->execute(['user_id' => $userId, 'slot_id' => $slotId]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'You are already in the waitlist for this slot.']);
        exit;
    }

    // Add to waitlist
    $stmt = $pdo->prepare("INSERT INTO waitlist (user_id, slot_id) VALUES (:user_id, :slot_id)");
    $stmt->execute(['user_id' => $userId, 'slot_id' => $slotId]);
    echo json_encode(['success' => true, 'message' => 'Slot is currently unavailable. You have been added to the waitlist.']);
    exit;
}

// Calculate cost
$start = new DateTime($startTime);
$end = new DateTime($endTime);
$hours = ($end->getTimestamp() - $start->getTimestamp()) / 3600;

$baseCost = 0;
$vehicleRate = 0;

// Get base rate
if ($durationType === 'hourly') {
    $baseCost = $hours * $slot['hourly_rate'];
} elseif ($durationType === 'daily') {
    $baseCost = ceil($hours / 24) * $slot['daily_rate'];
} elseif ($durationType === 'monthly') {
    $baseCost = ceil($hours / (24 * 30)) * $slot['monthly_rate'];
}

// Get vehicle rate
$vehicleColumn = $vehicleType . '_rate';
$vehicleRatePerUnit = $slot[$vehicleColumn] ?? 0;

if ($durationType === 'hourly') {
    $vehicleRate = $hours * $vehicleRatePerUnit;
} elseif ($durationType === 'daily') {
    $vehicleRate = ceil($hours / 24) * $vehicleRatePerUnit;
} elseif ($durationType === 'monthly') {
    $vehicleRate = ceil($hours / (24 * 30)) * $vehicleRatePerUnit;
}

$cost = $baseCost + $vehicleRate;

if ($cost <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking duration']);
    exit;
}

// Insert booking
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, slot_id, vehicle_type, start_time, end_time, status, amount) VALUES (:user_id, :slot_id, :vehicle_type, :start_time, :end_time, 'pending', :amount)");
    $stmt->execute([
        'user_id' => $userId,
        'slot_id' => $slotId,
        'vehicle_type' => $vehicleType,
        'start_time' => $startTime,
        'end_time' => $endTime,
        'amount' => $cost
    ]);
    $bookingId = $pdo->lastInsertId();

    // DON'T mark slot as unavailable here - wait until payment is completed
    // This prevents race conditions and ensures slots are only unavailable when actually in use

    // Update user statistics - increment total bookings count
    $stmt = $pdo->prepare("UPDATE users SET total_bookings = total_bookings + 1 WHERE id = :user_id");
    $stmt->execute(['user_id' => $userId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'booking_id' => $bookingId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
}
?>