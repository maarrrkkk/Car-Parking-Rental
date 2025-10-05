<?php
require_once './config/database.php';

$slot_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$slot_id) {
    header("Location: index.php?page=slots");
    exit;
}

// Fetch slot details
$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = :id");
$stmt->execute(['id' => $slot_id]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    header("Location: index.php?page=slots");
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);

// Vehicle types and their rates
$vehicleTypes = [
    'motorcycle' => ['name' => 'Motorcycle', 'rate' => $slot['motorcycle_rate']],
    'car' => ['name' => 'Car', 'rate' => $slot['car_rate']],
    'suv' => ['name' => 'SUV', 'rate' => $slot['suv_rate']],
    'van' => ['name' => 'Van', 'rate' => $slot['van_rate']],
    'truck' => ['name' => 'Truck', 'rate' => $slot['truck_rate']],
    'mini_truck' => ['name' => 'Mini Truck', 'rate' => $slot['mini_truck_rate']]
];
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Slot Image -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <img src="<?= htmlspecialchars($slot['image'] ?? 'assets/images/default.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($slot['name']) ?>" style="height: 400px; object-fit: cover;">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($slot['name']) ?></h3>
                        <p class="card-text">Premium parking space in Centennial City</p>
                    </div>
                </div>
            </div>

            <!-- Vehicle Rates -->
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0">Parking Rates by Vehicle Type</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Hourly Rates</h5>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($vehicleTypes as $key => $vehicle): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= $vehicle['name'] ?></span>
                                            <strong>₱<?= number_format($slot['hourly_rate'] + $vehicle['rate'], 2) ?>/hr</strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Daily Rates</h5>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($vehicleTypes as $key => $vehicle): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= $vehicle['name'] ?></span>
                                            <strong>₱<?= number_format($slot['daily_rate'] + $vehicle['rate'], 2) ?>/day</strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Monthly Rates</h5>
                            <div class="list-group list-group-flush">
                                <?php foreach ($vehicleTypes as $key => $vehicle): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?= $vehicle['name'] ?></span>
                                        <strong>₱<?= number_format(($slot['monthly_rate'] ?? 0) + $vehicle['rate'], 2) ?>/mon</strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button
                                class="btn btn-primary btn-lg w-100 <?= $slot['available'] ? '' : 'disabled' ?>"
                                onclick="handleBooking(<?= $slot['id'] ?>, <?= $isLoggedIn ? 'true' : 'false' ?>)">
                                <?= $slot['available'] ? 'Book This Slot' : 'Currently Unavailable' ?>
                            </button>
                            <a href="?page=slots" class="btn btn-outline-secondary btn-lg w-100 mt-2">Back to All Slots</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function handleBooking(slotId, isLoggedIn) {
    if (!isLoggedIn) {
        window.location.href = '?page=login';
        return;
    }
    // For now, redirect to booking page - will be updated to include vehicle selection
    window.location.href = '?page=booking&slot=' + slotId;
}
</script>