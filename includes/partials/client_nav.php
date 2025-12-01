<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <!-- Logo + Brand -->
        <a href="?page=home" class="navbar-brand d-flex align-items-center">
            <img src="assets/images/logo.png" alt="Car Parking Rental Logo" 
                 style="height: 50px; width: 100%; margin-right: 10px;">
        </a>

        <!-- Custom Hamburger -->
        <label for="check" class="menuButton d-lg-none ms-auto" id="customToggler">
            <input hidden="" class="check-icon" id="check" name="check" type="checkbox">
            <label class="icon-menu" for="check">
                <div class="bar bar--1"></div>
                <div class="bar bar--2"></div>
                <div class="bar bar--3"></div>
            </label>
        </label>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-3">
                <li class="nav-item">
                    <a href="?page=home" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a href="?page=about" class="nav-link <?= $page === 'about' ? 'active' : '' ?>">About</a>
                </li>
                <li class="nav-item">
                    <a href="?page=contact" class="nav-link <?= $page === 'contact' ? 'active' : '' ?>">Contact</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($page === 'profile') ? 'active' : '' ?>" 
                           href="#" id="profileDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="?page=profile">My Profile</a></li>
                            <li><a class="dropdown-item" href="includes/auth/logout_process.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Login Button -->
                    <li class="nav-item">
                        <a href="?page=login" class="btn btn-primary px-3 <?= $page === 'login' ? 'active' : '' ?>">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>