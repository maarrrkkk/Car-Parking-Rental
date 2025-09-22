<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/login.php');
    exit();
}

$current_page = isset($_GET['current_page']) ? $_GET['current_page'] : 'dashboard';
$validPages = ['dashboard', 'users', 'reports', 'booking', 'slots'];
if (!in_array($current_page, $validPages)) {
    $current_page = 'dashboard';
}

// Mock data for demonstration
$totalSpaces = 850;
$occupiedSpaces = 634;
$todayRevenue = 12450;
$totalUsers = 2847;
$newBookingsToday = 89;
$availableSpaces = $totalSpaces - $occupiedSpaces;
$occupancyRate = round(($occupiedSpaces / $totalSpaces) * 100, 1);

$recentBookings = [
    ['id' => 'BK001', 'user' => 'John Doe', 'slot' => 'Slot A', 'start_date' => '2024-01-09', 'end_date' => '2024-01-10', 'amount' => '$25.00', 'status' => 'Active'],
    ['id' => 'BK002', 'user' => 'Jane Smith', 'slot' => 'Slot B', 'start_date' => '2024-01-09', 'end_date' => '2024-01-09', 'amount' => '$15.00', 'status' => 'Completed'],
    ['id' => 'BK003', 'user' => 'Mike Johnson', 'slot' => 'Slot C', 'start_date' => '2024-01-09', 'end_date' => '2024-01-11', 'amount' => '$45.00', 'status' => 'Active'],
    ['id' => 'BK004', 'user' => 'Sarah Wilson', 'slot' => 'Slot D', 'start_date' => '2024-01-08', 'end_date' => '2024-01-08', 'amount' => '$12.00', 'status' => 'Completed'],
    ['id' => 'BK005', 'user' => 'David Brown', 'slot' => 'Slot E', 'start_date' => '2024-01-08', 'end_date' => '2024-01-09', 'amount' => '$30.00', 'status' => 'Active']
];

// Mock data for reports
$monthlyRevenue = [
    ['month' => 'Jan 2024', 'revenue' => 45000, 'bookings' => 1250, 'growth' => 15.2],
    ['month' => 'Feb 2024', 'revenue' => 52000, 'bookings' => 1380, 'growth' => 18.5],
    ['month' => 'Mar 2024', 'revenue' => 48000, 'bookings' => 1290, 'growth' => -8.1],
    ['month' => 'Apr 2024', 'revenue' => 55000, 'bookings' => 1450, 'growth' => 14.6],
    ['month' => 'May 2024', 'revenue' => 61000, 'bookings' => 1580, 'growth' => 10.9],
    ['month' => 'Jun 2024', 'revenue' => 68000, 'bookings' => 1720, 'growth' => 11.5],
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parking Rental Admin Dashboard</title>
    <link rel="shortcut icon" href="../../assets/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>

<body>
    <?php include '../../includes/partials/admin_nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../../includes/partials/admin_sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                
                <?php
                switch ($current_page) {
                    case 'dashboard':
                        include './dashboard.php';
                        break;
                    case 'users':
                        include './users.php';
                        break;
                    case 'booking':
                        include './booking.php';
                        break;
                    case 'reports':
                        include './reports.php';
                        break;
                    case 'slots':
                        include './slots.php';
                        break;
                }
                ?>
            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>

</html>