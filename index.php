<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$validPages = ['home', 'about', 'contact', 'login', 'register'];
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
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/main.css"> <!-- keep only overrides -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            default:
                include 'pages/client/home.php';
        }
        ?>
    </main>

    <?php include 'includes/partials/client_footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function handleBooking(spaceId) {
            <?php if (isset($_SESSION['user_id'])): ?>
                // User logged in → redirect to booking page
                window.location.href = "index.php?page=booking&id=" + spaceId;
            <?php else: ?>
                // User not logged in → redirect to login page
                window.location.href = "index.php?page=login&status=error&message=Please login first.";
            <?php endif; ?>
        }

        function handleViewDetails(spaceId) {
            <?php if (isset($_SESSION['user_id'])): ?>
                // User logged in → redirect to details page
                window.location.href = "index.php?page=details&id=" + spaceId;
            <?php else: ?>
                // User not logged in → redirect to login page
                window.location.href = "index.php?page=login&status=error&message=Please login first.";
            <?php endif; ?>
        }
    </script>

</body>



</html>