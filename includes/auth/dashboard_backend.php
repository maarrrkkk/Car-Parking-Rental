<?php
require_once '../../config/database.php'; // Connect to the database
require_once '../../includes/auth/fetch_data.php'; // We'll use the $bookings from here


// ======================================================
// Dashboard Backend Logic - Replacing Mock Data
// ======================================================
try {
    // Card 1: Total Parking Spaces (from slots table)
    $totalSpaces = $pdo->query("SELECT COUNT(*) FROM slots")->fetchColumn();

    // Card 2: Today's Revenue (from bookings table)
    // Sums the 'amount' from bookings that are 'completed' today.
    $stmtRevenue = $pdo->prepare("SELECT SUM(amount) FROM bookings WHERE status = 'completed' AND DATE(paid_at) = CURDATE()");
    $stmtRevenue->execute();
    $todayRevenue = $stmtRevenue->fetchColumn() ?: 0.00; // Use 0 if no revenue today

    // Card 3: Occupancy Rate
    // Counts bookings that are currently 'active'.
    $occupiedSpaces = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'active'")->fetchColumn();
    $availableSpaces = $totalSpaces - $occupiedSpaces; // [cite: 29]
    // Avoid division by zero if there are no spaces
    $occupancyRate = ($totalSpaces > 0) ? round(($occupiedSpaces / $totalSpaces) * 100, 1) : 0;

    // Card 4: New Bookings Today
    // Counts bookings created today.
    $stmtNewBookings = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE DATE(created_at) = CURDATE()");
    $stmtNewBookings->execute();
    $newBookingsToday = $stmtNewBookings->fetchColumn();
    
    // Extra Stat: Total Users
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // Recent Bookings (Using the limited query from fetch_data.php)
    $recentBookings = $bookings; 

} catch (PDOException $e) {
    // In case of a database error, set default values to avoid breaking the page
    $totalSpaces = 0;
    $todayRevenue = 0;
    $occupiedSpaces = 0;
    $occupancyRate = 0;
    $newBookingsToday = 0;
    $totalUsers = 0;
    $recentBookings = [];
    // Optional: You could log the error or display a friendly message
    // error_log("Dashboard query failed: " . $e->getMessage());
}