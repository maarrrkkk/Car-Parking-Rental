<?php

// Always load database setup on site start
require_once __DIR__ . '/config/database.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$validPages = ['home', 'about', 'contact', 'login', 'register', 'verify_email', 'forgot_password', 'reset_password', 'slots', 'slot_details', 'booking', 'profile'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parking Rental</title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/main.css"> <!-- keep only overrides -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>

    <?php include 'includes/partials/client_nav.php'; ?>


    <!-- Main Content -->
    <main class="main-content">
        <?php
        switch ($page) {
            case 'home':
                include 'pages/home.php';
                break;
            case 'about':
                include 'pages/about.php';
                break;
            case 'contact':
                include 'pages/contact.php';
                break;
            case 'login':
                include 'pages/auth/login.php';
                break;
            case 'register':
                include 'pages/auth/register.php';
                break;
            case 'verify_email':
                include 'pages/auth/verify_email.php';
                break;
            case 'forgot_password':
                include 'pages/auth/forgot_password.php';
                break;
            case 'reset_password':
                include 'pages/auth/reset_password.php';
                break;
            case 'slots':
                include 'pages/client/slots.php';
                break;
            case 'slot_details':
                include 'pages/client/slot_details.php';
                break;
            case 'booking':
                include 'pages/client/booking.php';
                break;
            case 'profile':
                include 'pages/client/profile.php';
                break;
            default:
                include 'pages/home.php';
        }
        ?>
    </main>

    <?php include 'includes/partials/client_footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>



</html>

