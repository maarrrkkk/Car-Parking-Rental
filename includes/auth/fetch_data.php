<?php
require_once "../../config/database.php";

// ==========================
// Fetch Users
// ==========================
try {
    $stmtUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
}

// ==========================
// Fetch Slots
// ==========================
try {
    $stmtSlots = $pdo->query("SELECT * FROM slots ORDER BY name ASC");
    $slots = $stmtSlots->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $slots = [];
}

// ==========================
// Fetch Bookings (with User + Slot info)
// ==========================
try {
    // MODIFIED: Added LIMIT 5 to get only the most recent bookings for the dashboard
    $stmtBookings = $pdo->query("
        SELECT b.*, u.firstname, u.lastname, s.name AS slot_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN slots s ON b.slot_id = s.id
        ORDER BY b.created_at DESC
        LIMIT 5 
    ");
    $bookings = $stmtBookings->fetchAll(PDO::FETCH_ASSOC); // 
} catch (PDOException $e) {
    $bookings = []; // [cite: 7]
}

// ==========================
// Fetch Payments (with Booking + User info)
// ==========================
try {
    $stmtPayments = $pdo->query("
        SELECT p.*, b.start_time, b.end_time, u.firstname, u.lastname
        FROM payments p
        JOIN bookings b ON p.booking_id = b.id
        JOIN users u ON b.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $payments = $stmtPayments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payments = [];
}
