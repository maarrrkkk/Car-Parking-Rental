<?php
require_once '../../includes/auth/slots_data.php';
require_once "../../includes/auth/fetch_data.php";


// Auto-generate next slot name
$slotCount = count($slots);
$nextSlotName = "Slot " . chr(65 + $slotCount);
?>

<section>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Parking Slots</h1>
    </div>
    <!-- Add Slot Form -->
    <div class="card mb-4">
        <div class="card-header">Add New Slot</div>
        <div class="card-body">
            <form id="addSlotForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="row mb-2">
                    <!-- Auto Slot Name -->
                    <div class="col-md-2">
                        <input type="text" name="name" class="form-control"
                            value="<?= $nextSlotName ?>" readonly>
                    </div>

                    <div class="col-md-2">
                        <input type="number" step="0.01" name="hourly_rate"
                            class="form-control" placeholder="Hourly Rate" required>
                    </div>

                    <div class="col-md-2">
                        <input type="number" step="0.01" name="daily_rate"
                            class="form-control" placeholder="Daily Rate" required>
                    </div>

                    <div class="col-md-3">
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="col-md-1 text-center">
                        <input type="checkbox" name="available" checked> Available
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Slots Table -->
    <div class="card">
        <div class="card-header">All Slots</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Hourly Rate</th>
                        <th>Daily Rate</th>
                        <th>Available</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr>
                            <td><?= htmlspecialchars($slot['name']) ?></td>
                            <td><?= number_format($slot['hourly_rate'], 2) ?></td>
                            <td><?= number_format($slot['daily_rate'], 2) ?></td>
                            <td><?= $slot['available'] ? 'Yes' : 'No' ?></td>
                            <td>
                                <?php if ($slot['image']): ?>
                                    <img src="../../<?= htmlspecialchars($slot['image']) ?>" alt="Slot Image" width="80">
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Update Form -->
                                <form method="POST" enctype="multipart/form-data" style="display:inline-block;">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= $slot['id'] ?>">
                                    <input type="number" step="0.01" name="hourly_rate" value="<?= $slot['hourly_rate'] ?>" class="form-control mb-1">
                                    <input type="number" step="0.01" name="daily_rate" value="<?= $slot['daily_rate'] ?>" class="form-control mb-1">
                                    <input type="file" name="image" class="form-control mb-1">
                                    <label><input type="checkbox" name="available" <?= $slot['available'] ? 'checked' : '' ?>> Available</label>
                                    <button type="submit" class="btn btn-warning btn-sm mt-1">Update</button>
                                </form>

                                <!-- Delete Form -->
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this slot?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $slot['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</section>

<script>
    document.getElementById("addSlotForm").addEventListener("submit", function() {
        // Wait a little so PHP inserts first before reset (optional)
        setTimeout(() => this.reset(), 100);
    });
</script>