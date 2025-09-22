<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="?current_page=dashboard" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="?current_page=slots" class="nav-link <?= $current_page === 'slots.php' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i> Parking Slots
                </a>
            </li>
            <li class="nav-item">
                <a href="?current_page=users" class="nav-link <?= $current_page === 'users.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a href="?current_page=reports" class="nav-link <?= $current_page === 'reports.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>
        </ul>
    </div>
</nav>
