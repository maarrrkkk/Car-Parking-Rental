<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="?current_page=dashboard" class="nav-link <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="?current_page=slots" class="nav-link <?= $current_page === 'slots' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i> Parking Slots
                </a>
            </li>
            <li class="nav-item">
                <a href="?current_page=users" class="nav-link <?= $current_page === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a href="?current_page=reports" class="nav-link <?= $current_page === 'reports' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </li>

        </ul>
    </div>
</div>