<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top shadow">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="../../assets/images/logo.png" alt="Logo" class="me-2" style="height:30px;">
      <span class="d-none d-sm-inline">Car Parking Rental - Admin</span>
    </a>

    <!-- Sidebar toggle (for admin layout) -->
    <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle sidebar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- User menu toggle (collapses on mobile) -->
    <div class="dropdown ms-auto">
      <a class="nav-link text-white dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle me-1"></i> <span class="d-none d-sm-inline">Admin</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <a class="dropdown-item" href="../../includes/auth/logout_process.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </ul>
    </div>
  </div>
</nav>
