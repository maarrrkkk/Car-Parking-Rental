<?php
require_once '../../config/database.php'; // Connect to DB

try {
    // Fetch all bookings with related user + slot info
    $stmtAllBookings = $pdo->query("
        SELECT b.id,
               CONCAT(u.firstname, ' ', u.lastname) AS user,
               u.email,
               s.name AS slot,
               b.start_time,
               b.end_time,
               b.amount,
               b.status,
               b.receipt,
               b.created_at
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN slots s ON b.slot_id = s.id
        ORDER BY b.created_at DESC
    ");
    $allBookings = $stmtAllBookings->fetchAll(PDO::FETCH_ASSOC);

    // Stats
    $totalBookings = count($allBookings);
    $activeBookings = count(array_filter($allBookings, fn($b) => strtolower($b['status']) === 'active'));
    $completedBookings = count(array_filter($allBookings, fn($b) => strtolower($b['status']) === 'completed'));

    // Today's revenue
    $stmtRevenue = $pdo->prepare("
        SELECT SUM(amount)
        FROM bookings
        WHERE DATE(paid_at) = CURDATE() AND status = 'completed'
    ");
    $stmtRevenue->execute();
    $todayRevenue = $stmtRevenue->fetchColumn() ?: 0;

} catch (PDOException $e) {
    $allBookings = [];
    $totalBookings = 0;
    $activeBookings = 0;
    $completedBookings = 0;
    $todayRevenue = 0;
}
