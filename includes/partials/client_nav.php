<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <div class="d-flex align-items-start" onclick="location.href='?page=home'" style="cursor: pointer;">
            <img src="assets/images/logo.png" alt="Car Parking Rental Logo" style="height: 40px; width: 40px; margin-right: 10px;">
            <a class="navbar-brand fw-bold">Car Parking Rental</a>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="?page=home" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">Home</a></li>
                <li class="nav-item"><a href="?page=about" class="nav-link <?= $page === 'about' ? 'active' : '' ?>">About</a></li>
                <li class="nav-item"><a href="?page=contact" class="nav-link <?= $page === 'contact' ? 'active' : '' ?>">Contact</a></li>
                <li class="nav-item"><a href="?page=login" class="nav-link login_button <?= $page === 'login' ? 'active' : '' ?>">Login</a></li>
            </ul>
        </div>
    </div>
</nav>