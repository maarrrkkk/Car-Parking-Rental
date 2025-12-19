<?php
include '../../includes/auth/admin_auth.php';
require_once "../../includes/auth/fetch_data.php";

// Auto-generate next slot name
$slotCount = count($slots);
$nextSlotName = "Slot " . chr(65 + $slotCount);

// Display messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['success_message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['error_message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['warning_message'])) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['warning_message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['warning_message']);
}
?>

<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Parking Slots</h1>
    </div>
    <!-- Add Slot Form -->
    <div class="card mb-4">
        <div class="card-header">Add New Slot</div>
        <div class="card-body">
            <form id="addSlotForm" method="POST" action="../../includes/auth/slots_data.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="row g-3 align-items-end">

                    <div class="col-12 col-lg-6">
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label">Slot Name</label>
                                <input type="hidden" name="name" value="<?= $nextSlotName ?>">
                                <div class="form-control-plaintext ps-2">
                                    <strong><?= $nextSlotName ?></strong>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" class="form-control" placeholder="Hourly Rate" required>
                                    <label for="hourly_rate">Hourly Rate *</label>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="daily_rate" id="daily_rate" class="form-control" placeholder="Daily Rate" required>
                                    <label for="daily_rate">Daily Rate *</label>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="monthly_rate" id="monthly_rate" class="form-control" placeholder="Monthly Rate" required>
                                    <label for="monthly_rate">Monthly Rate *</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3">
                        <label for="image" class="form-label">Slot Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                        <div class="form-check mt-3 ps-4">
                            <input class="form-check-input" type="checkbox" name="available" id="available" checked>
                            <label class="form-check-label" for="available">
                                Mark as Available
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-lg-3">
                        <button type="submit" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus-circle me-2"></i>Add Slot
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Slots Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">All Slots</h6>
            <span class="text-muted small">Total: <?= count($slots) ?></span>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>
                            Hourly Rate
                            <span
                                class="text-primary"
                                data-bs-toggle="tooltip"
                                title="Required field">
                                <i class="bi bi-info-circle text-warning"></i>
                            </span>
                        </th>
                        <th>
                            Daily Rate
                            <span
                                class="text-primary"
                                data-bs-toggle="tooltip"
                                title="Required field">
                                <i class="bi bi-info-circle text-warning"></i>
                            </span>
                        </th>
                        <th>
                            Monthly Rate
                            <span
                                class="text-primary"
                                data-bs-toggle="tooltip"
                                title="Required field">
                                <i class="bi bi-info-circle text-warning"></i>
                            </span>
                        </th>
                        <th>Available</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr>
                            <!-- Slot Name -->
                            <td>
                                <div class="d-flex justify-content-start">
                                    <strong><?= htmlspecialchars($slot['name']) ?></strong>
                                </div>
                            </td>

                            <!-- Hourly Rate -->
                            <td>
                                <div class="d-flex justify-content-start">
                                    ₱<?= number_format($slot['hourly_rate'], 2) ?>
                                </div>
                            </td>

                            <!-- Daily Rate -->
                            <td>
                                <div class="d-flex justify-content-start">
                                    ₱<?= number_format($slot['daily_rate'], 2) ?>
                                </div>
                            </td>

                            <!-- Monthly Rate -->
                            <td>
                                <div class="d-flex justify-content-start">
                                    ₱<?= number_format($slot['monthly_rate'], 2) ?>
                                </div>
                            </td>

                            <!-- Availability -->
                            <td>
                                <div class="d-flex justify-content-start">
                                    <?php if ($slot['available']): ?>
                                        <span class="badge bg-success">Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Unavailable</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Image -->
                            <td>
                                <div class="d-flex justify-content-start">
                                    <?php if ($slot['image']): ?>
                                        <a href="../../<?= htmlspecialchars($slot['image']) ?>" target="_blank">
                                            <img src="../../<?= htmlspecialchars($slot['image']) ?>"
                                                alt="Slot Image" class="img-thumbnail" style="width:60px; height:auto;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="d-flex flex-column justify-content-reverse">
                                <div class="d-flex justify-content-sm-center justify-content-md-start flex-wrap gap-1">
                                    <!-- Edit button -->
                                    <button class="btn btn-warning btn-sm" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#editSlot<?= $slot['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Delete button -->
                                    <form method="POST" action="../../includes/auth/slots_data.php" onsubmit="return confirm('Delete this slot?')" class="m-0">
                                        <input type="hidden" name="id" value="<?= $slot['id'] ?>">
                                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Inline Edit Form (collapsible) -->
                                <div class="collapse mt-2" id="editSlot<?= $slot['id'] ?>">
                                    <form method="POST" action="../../includes/auth/slots_data.php" enctype="multipart/form-data" class="p-2 border rounded bg-light">
                                        <input type="hidden" name="id" value="<?= $slot['id'] ?>">

                                        <input type="number" step="0.01" name="hourly_rate"
                                            value="<?= $slot['hourly_rate'] ?>" class="form-control mb-2" placeholder="Hourly Rate *" required>

                                        <input type="number" step="0.01" name="daily_rate"
                                            value="<?= $slot['daily_rate'] ?>" class="form-control mb-2" placeholder="Daily Rate *" required>

                                        <input type="number" step="0.01" name="monthly_rate"
                                            value="<?= $slot['monthly_rate'] ?>" class="form-control mb-2" placeholder="Monthly Rate *" required>

                                        <input type="file" name="image" class="form-control mb-2">

                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="available" class="form-check-input"
                                                <?= $slot['available'] ? 'checked' : '' ?>>
                                            <label class="form-check-label">Available</label>
                                        </div>

                                        <button type="submit" name="action" value="update" class="btn btn-primary w-100">
                                            Save Changes
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>
    </div>
</section>


<script>
    function confirmAction(event) {
        const action = event.submitter.value; // detects which button was clicked
        if (action === "delete") {
            if (!confirm("Delete this slot?")) {
                event.preventDefault(); // cancel form submission
                return false;
            }
        }
        return true;
    }
</script>

<script>
    document.getElementById("addSlotForm").addEventListener("submit", function() {
        // Wait a little so PHP inserts first before reset (optional)
        setTimeout(() => this.reset(), 100);
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>