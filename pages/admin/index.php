<?php
include '../../includes/auth/admin_auth.php';

$current_page = isset($_GET['current_page']) ? $_GET['current_page'] : 'dashboard';
$validPages = ['dashboard', 'users', 'reports', 'booking', 'slots', 'profile', 'edit_profile'];
if (!in_array($current_page, $validPages)) {
    $current_page = 'dashboard';
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parking Rental Admin Dashboard</title>
    <link rel="shortcut icon" href="../../assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>

<body>
    <?php include '../../includes/partials/admin_nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <!-- Sidebar -->
            <nav id="sidebarMenu" class="d-md-block sidebar collapse">
                <div class="position-sticky">
                    <?php include '../../includes/partials/admin_sidebar.php'; ?>
                </div>
            </nav>

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
                    case 'profile':
                        include '../..//includes/partials/profile.php';
                        break;
                    case 'edit_profile':
                        include '../..//includes/partials/edit_profile.php';
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