<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top shadow">
  <div class="container-fluid nav-padding">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="../../assets/images/logo.png" alt="Logo" class="me-2" style="height:30px;">
      <span class="d-none d-sm-inline">Car Parking Rental - Admin</span>
    </a>

    <!-- User menu toggle -->
    <div class="dropdown ms-auto">
      <a class="nav-link text-white dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle me-1"></i> <span class="d-none d-sm-inline">Admin</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2" style="min-width: 220px;">
        <!-- User Info Header -->
        <li class="px-3 py-2 text-center border-bottom">
          <i class="fas fa-user-circle fa-2x text-primary mb-1"></i>
          <div class="fw-semibold">
              <?= htmlspecialchars($_SESSION['name']); ?>
          </div>
          <small class="text-muted">
              <?= ucfirst(htmlspecialchars($_SESSION['role'])); ?>
          </small>
        </li>

        <!-- Menu Items -->
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2"  href="?current_page=profile" <?= $current_page === 'profile' ? 'active' : '' ?>>
            <i class="fas fa-user text-primary"></i> <span>Profile</span>
          </a>
        </li>
        
        <!-- Admin Settings -->
        <!-- <li>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="settings.php">
            <i class="fas fa-cog text-secondary"></i> <span>Settings</span>
          </a>
        </li> -->

        <li>
          <hr class="dropdown-divider my-2">
        </li>

        <!-- Logout -->
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2 text-danger fw-semibold py-2"
            href="../../includes/auth/logout_process.php">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
          </a>
        </li>
      </ul>

    </div>

    <!-- Sidebar toggle -->
    <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle sidebar">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>