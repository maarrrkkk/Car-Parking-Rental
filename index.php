<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$validPages = ['home', 'about', 'contact', 'login'];
if (!in_array($page, $validPages)) {
    $page = 'home';
}

// If login page, load it directly and stop
if ($page === 'login') {
    include 'pages/auth/user-access.php';
    exit; // 👈 Prevents navbar & rest of layout from rendering
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parking Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/main.css"> <!-- keep only overrides -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="?page=home">Car Parking Rental</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="?page=home" class="nav-link <?= $page==='home'?'active':'' ?>">Home</a></li>
                <li class="nav-item"><a href="?page=about" class="nav-link <?= $page==='about'?'active':'' ?>">About</a></li>
                <li class="nav-item"><a href="?page=contact" class="nav-link <?= $page==='contact'?'active':'' ?>">Contact</a></li>
                <li class="nav-item"><a href="?page=login" class="nav-link">Login</a></li>
            </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        switch ($page) {
            case 'home':
                include 'pages/client/home.php';
                break;
            case 'about':
                include 'pages/client/about.php';
                break;
            case 'contact':
                include 'pages/client/contact.php';
                break;
            default:
                include 'pages/client/home.php';
        }
        ?>
    </main>
    <!-- Footer -->
    <footer class="bg-dark text-light text-center text-lg-start mt-5">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase fw-bold">Car Parking Rental</h5>
                    <p>
                        Find and rent parking spaces easily. Safe, convenient, and affordable solutions for your parking needs.
                    </p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Links</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="?page=home" class="text-light">Home</a></li>
                        <li><a href="?page=about" class="text-light">About</a></li>
                        <li><a href="?page=contact" class="text-light">Contact</a></li>
                        <li><a href="?page=login" class="text-light">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> info@carparkingrental.com</li>
                        <li><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>
 
</html>
