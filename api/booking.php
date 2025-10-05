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
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'pending', receipt = :receipt, paid_at = NOW() WHERE id = :id");
        $stmt->execute([
            'receipt' => 'assets/images/receipts/' . $filename,
            'id' => $bookingId
        ]);

        echo json_encode(['success' => true, 'message' => 'Payment confirmed successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Payment confirmation failed: ' . $e->getMessage()]);
    }
    exit;
}

if (!$slotId || !$startTime || !$endTime || !$durationType || !$vehicleType) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate slot is available
$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = :id AND available = 1");
$stmt->execute(['id' => $slotId]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    echo json_encode(['success' => false, 'message' => 'Slot not available']);
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

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'booking_id' => $bookingId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()]);
}
?>