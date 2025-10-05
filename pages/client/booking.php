<?php
require_once __DIR__ . '/../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /Github/Car-Parking-Rental/index.php?page=login&status=error&message=Please login first.");
    exit;
}

// Get slot ID from URL
$slotId = $_GET['id'] ?? null;
if (!$slotId) {
    header("Location: /Github/Car-Parking-Rental/index.php?page=home&status=error&message=Invalid slot.");
    exit;
}

// Fetch slot details
$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = :id AND available = 1");
$stmt->execute(['id' => $slotId]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    header("Location: /Github/Car-Parking-Rental/index.php?page=home&status=error&message=Slot not available.");
    exit;
}

// Determine available duration types
$availableTypes = [];
if ($slot['hourly_rate'] > 0) $availableTypes[] = 'hourly';
if ($slot['daily_rate'] > 0) $availableTypes[] = 'daily';
if ($slot['monthly_rate'] > 0) $availableTypes[] = 'monthly';

// Default to first available or daily
$defaultType = $availableTypes[0] ?? 'daily';
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Book Parking Slot: <?= htmlspecialchars($slot['name']) ?></h4>
                </div>
                <div class="card-body">
                    <!-- Slot Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <img src="<?= htmlspecialchars($slot['image'] ?? 'assets/images/parking_slots/default.jpg') ?>"
                                alt="Slot Image" class="img-fluid rounded">
                        </div>
                        <div class="col-md-6">
                            <h5>Rates:</h5>
                            <?php if ($slot['hourly_rate'] > 0): ?>
                                <p>Hourly: ₱<?= number_format($slot['hourly_rate'], 2) ?></p>
                            <?php endif; ?>
                            <?php if ($slot['daily_rate'] > 0): ?>
                                <p>Daily: ₱<?= number_format($slot['daily_rate'], 2) ?></p>
                            <?php endif; ?>
                            <?php if ($slot['monthly_rate'] > 0): ?>
                                <p>Monthly: ₱<?= number_format($slot['monthly_rate'], 2) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Booking Form -->
                    <form id="bookingForm">
                        <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">

                        <?php if (count($availableTypes) > 1): ?>
                        <div class="mb-3">
                            <label for="duration_type" class="form-label">Duration Type</label>
                            <select class="form-select" id="duration_type" name="duration_type" required>
                                <?php foreach ($availableTypes as $type): ?>
                                    <option value="<?= $type ?>"><?= ucfirst($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" id="duration_type" name="duration_type" value="<?= $defaultType ?>">
                        <?php endif; ?>

                        <!-- Hourly Fields -->
                        <div class="mb-3" id="hourlyFields" style="display: <?= $defaultType === 'hourly' ? 'block' : 'none' ?>;">
                            <label for="start_time_hourly" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time_hourly" name="start_time_hourly" <?= $defaultType === 'hourly' ? 'required' : '' ?>>
                            <label for="end_time_hourly" class="form-label mt-2">End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time_hourly" name="end_time_hourly" <?= $defaultType === 'hourly' ? 'required' : '' ?>>
                        </div>

                        <!-- Daily Fields -->
                        <div class="mb-3" id="dailyFields" style="display: <?= $defaultType === 'daily' ? 'block' : 'none' ?>;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" <?= $defaultType === 'daily' ? 'required' : '' ?>>
                            <label for="end_date" class="form-label mt-2">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" <?= $defaultType === 'daily' ? 'required' : '' ?>>
                        </div>

                        <!-- Monthly Fields -->
                        <div class="mb-3" id="monthlyFields" style="display: <?= $defaultType === 'monthly' ? 'block' : 'none' ?>;">
                            <label for="start_date_monthly" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date_monthly" name="start_date_monthly" <?= $defaultType === 'monthly' ? 'required' : '' ?>>
                            <p class="mt-2 text-muted" id="monthlyEndDate">End Date: Select a start date</p>
                        </div>

                        <div class="mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                                <option value="motorcycle">Motorcycle (+₱<?= number_format($slot['motorcycle_rate'], 2) ?>)</option>
                                <option value="car">Car (+₱<?= number_format($slot['car_rate'], 2) ?>)</option>
                                <option value="suv">SUV (+₱<?= number_format($slot['suv_rate'], 2) ?>)</option>
                                <option value="van">Van (+₱<?= number_format($slot['van_rate'], 2) ?>)</option>
                                <option value="truck">Truck (+₱<?= number_format($slot['truck_rate'], 2) ?>)</option>
                                <option value="mini_truck">Mini Truck (+₱<?= number_format($slot['mini_truck_rate'], 2) ?>)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <p class="text-muted">Estimated Cost: <span id="estimatedCost">₱0.00</span></p>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn">Proceed to Payment</button>
                        <a href="/Github/Car-Parking-Rental/index.php?page=home" class="btn btn-secondary">Cancel</a>
                    </form>

                    <!-- Payment Step -->
                    <div id="paymentStep" style="display: none;">
                        <input type="hidden" id="bookingId" name="bookingId">
                        <h5 class="mb-3">Payment Instructions</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Scan QR Code to Pay</h6>
                                <img src="assets/images/gcashQrcode/<?php
                                    $qrFiles = glob('assets/images/gcashQrcode/*.png') ?: glob('assets/images/gcashQrcode/*.jpg') ?: glob('assets/images/gcashQrcode/*.jpeg') ?: [];
                                    echo !empty($qrFiles) ? basename($qrFiles[0]) : 'gcash_qr.png';
                                ?>" alt="GCash QR Code" class="img-fluid" style="max-width: 200px;">
                                <p class="mt-2"><strong>Amount: ₱<span id="paymentAmount">0.00</span></strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6>How to Pay:</h6>
                                <ol>
                                    <li>Open your GCash app</li>
                                    <li>Tap "Scan QR"</li>
                                    <li>Scan the QR code above</li>
                                    <li>Enter the amount: ₱<span id="paymentAmount2">0.00</span></li>
                                    <li>Confirm the payment</li>
                                    <li>Take a screenshot of the receipt</li>
                                </ol>
                                <div class="mb-3">
                                    <label for="receipt" class="form-label">Upload Payment Receipt</label>
                                    <input type="file" class="form-control" id="receipt" name="receipt" accept="image/*" required>
                                    <div class="form-text">Upload a screenshot of your GCash payment receipt</div>
                                </div>
                                <p class="text-muted">After uploading the receipt, click "Confirm Payment" below.</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success" id="confirmPaymentBtn">Confirm Payment</button>
                            <button type="button" class="btn btn-secondary" id="backToFormBtn">Back to Booking</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle form fields based on duration type
    function toggleFields() {
        const durationType = document.getElementById('duration_type').value;
        document.getElementById('hourlyFields').style.display = durationType === 'hourly' ? 'block' : 'none';
        document.getElementById('dailyFields').style.display = durationType === 'daily' ? 'block' : 'none';
        document.getElementById('monthlyFields').style.display = durationType === 'monthly' ? 'block' : 'none';

        // Set required attributes
        document.getElementById('start_time_hourly').required = durationType === 'hourly';
        document.getElementById('end_time_hourly').required = durationType === 'hourly';
        document.getElementById('start_date').required = durationType === 'daily';
        document.getElementById('end_date').required = durationType === 'daily';
        document.getElementById('start_date_monthly').required = durationType === 'monthly';

        calculateCost();
    }

    // Calculate estimated cost
    function calculateCost() {
        const durationType = document.getElementById('duration_type').value;
        const vehicleType = document.getElementById('vehicle_type').value;
        let hours = 0;
        let days = 0;
        let months = 0;
        let actualDurationType = durationType;

        if (durationType === 'hourly') {
            const startTime = document.getElementById('start_time_hourly').value;
            const endTime = document.getElementById('end_time_hourly').value;
            if (startTime && endTime) {
                const start = new Date(startTime);
                const end = new Date(endTime);
                if (start < end) {
                    hours = (end - start) / (1000 * 60 * 60);
                }
            }
        } else if (durationType === 'daily') {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1; // inclusive
                if (days >= 30 && <?= $slot['monthly_rate'] ?> > 0) {
                    actualDurationType = 'monthly';
                    months = Math.ceil(days / 30);
                } else {
                    hours = days * 24;
                }
            }
        } else if (durationType === 'monthly') {
            const startDate = document.getElementById('start_date_monthly').value;
            if (startDate) {
                const start = new Date(startDate);
                const end = new Date(start);
                end.setDate(start.getDate() + 30);
                document.getElementById('monthlyEndDate').textContent = 'End Date: ' + end.toISOString().slice(0, 10);
                months = 1;
                hours = 24 * 30;
            }
        }

        if ((durationType === 'hourly' && hours > 0) ||
            (durationType === 'daily' && days > 0) ||
            (durationType === 'monthly' && months > 0)) {

            // For hourly, ensure valid time range
            if (durationType === 'hourly') {
                const start = new Date(document.getElementById('start_time_hourly').value);
                const end = new Date(document.getElementById('end_time_hourly').value);
                if (start >= end) {
                    document.getElementById('estimatedCost').textContent = 'Invalid time range';
                    return;
                }
            }

            let baseCost = 0;
            let vehicleRate = 0;

            // Get base rate
            if (actualDurationType === 'hourly') {
                baseCost = hours * <?= $slot['hourly_rate'] ?>;
            } else if (actualDurationType === 'daily') {
                baseCost = days * <?= $slot['daily_rate'] ?>;
            } else if (actualDurationType === 'monthly') {
                baseCost = months * <?= $slot['monthly_rate'] ?>;
            }

            // Get vehicle rate
            const vehicleRates = {
                'motorcycle': <?= $slot['motorcycle_rate'] ?>,
                'car': <?= $slot['car_rate'] ?>,
                'suv': <?= $slot['suv_rate'] ?>,
                'van': <?= $slot['van_rate'] ?>,
                'truck': <?= $slot['truck_rate'] ?>,
                'mini_truck': <?= $slot['mini_truck_rate'] ?>
            };

            vehicleRate = vehicleRates[vehicleType] || 0;

            // Apply vehicle rate based on duration
            if (actualDurationType === 'hourly') {
                vehicleRate *= hours;
            } else if (actualDurationType === 'daily') {
                vehicleRate *= days;
            } else if (actualDurationType === 'monthly') {
                vehicleRate *= months;
            }

            const totalCost = baseCost + vehicleRate;
            document.getElementById('estimatedCost').textContent = '₱' + totalCost.toFixed(2);

            // Update duration type display if switched
            if (actualDurationType !== durationType) {
                document.getElementById('duration_type').value = actualDurationType;
                toggleFields();
            }
        } else {
            document.getElementById('estimatedCost').textContent = '₱0.00';
        }
    }

    // Initial toggle
    toggleFields();

    // Event listeners
    document.getElementById('duration_type').addEventListener('change', function() {
        toggleFields();
        calculateCost();
    });
    document.getElementById('start_time_hourly').addEventListener('change', calculateCost);
    document.getElementById('end_time_hourly').addEventListener('change', calculateCost);
    document.getElementById('start_date').addEventListener('change', calculateCost);
    document.getElementById('end_date').addEventListener('change', calculateCost);
    document.getElementById('start_date_monthly').addEventListener('change', calculateCost);
    document.getElementById('vehicle_type').addEventListener('change', calculateCost);

    // Form submission - Proceed to Payment
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form
        const durationType = document.getElementById('duration_type').value;
        let isValid = true;

        if (durationType === 'hourly') {
            const start = document.getElementById('start_time_hourly').value;
            const end = document.getElementById('end_time_hourly').value;
            if (!start || !end || new Date(start) >= new Date(end)) {
                alert('Please select valid start and end times for hourly booking.');
                isValid = false;
            }
        } else if (durationType === 'daily') {
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            if (!start || !end || new Date(start) > new Date(end)) {
                alert('Please select valid start and end dates for daily booking.');
                isValid = false;
            }
        } else if (durationType === 'monthly') {
            const start = document.getElementById('start_date_monthly').value;
            if (!start) {
                alert('Please select a start date for monthly booking.');
                isValid = false;
            }
        }

        if (!isValid) return;

        // Create booking first
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating Booking...';

        const formData = new FormData(this);
        const durationTypeVal = document.getElementById('duration_type').value;

        // Generate start_time and end_time based on inputs
        let startTime, endTime;
        if (durationTypeVal === 'hourly') {
            startTime = document.getElementById('start_time_hourly').value.replace('T', ' ');
            endTime = document.getElementById('end_time_hourly').value.replace('T', ' ');
        } else if (durationTypeVal === 'daily') {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            startTime = startDate + ' 00:00:00';
            endTime = endDate + ' 23:59:59';
        } else if (durationTypeVal === 'monthly') {
            const startDate = document.getElementById('start_date_monthly').value;
            startTime = startDate + ' 00:00:00';
            const start = new Date(startDate);
            const end = new Date(start);
            end.setDate(start.getDate() + 30);
            endTime = end.toISOString().slice(0, 10) + ' 23:59:59';
        }

        formData.append('start_time', startTime);
        formData.append('end_time', endTime);

        fetch('/Github/Car-Parking-Rental/api/booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store booking ID
                    document.getElementById('bookingId').value = data.booking_id;

                    // Show payment step
                    document.getElementById('bookingForm').style.display = 'none';
                    document.getElementById('paymentStep').style.display = 'block';

                    // Update payment amount
                    const amount = document.getElementById('estimatedCost').textContent.replace('₱', '');
                    document.getElementById('paymentAmount').textContent = amount;
                    document.getElementById('paymentAmount2').textContent = amount;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error(error);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Proceed to Payment';
            });
    });

    // Confirm Payment
    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        const confirmBtn = this;
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        const formData = new FormData();
        formData.append('confirm_payment', '1');
        formData.append('booking_id', document.getElementById('bookingId').value);

        const receiptFile = document.getElementById('receipt').files[0];
        if (receiptFile) {
            formData.append('receipt', receiptFile);
        } else {
            alert('Please upload your payment receipt.');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Payment';
            return;
        }

        fetch('/Github/Car-Parking-Rental/api/booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment confirmed successfully! Your booking is now pending approval.');
                    window.location.href = '/Github/Car-Parking-Rental/index.php?page=profile';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error(error);
            })
            .finally(() => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm Payment';
            });
    });

    // Back to Form
    document.getElementById('backToFormBtn').addEventListener('click', function() {
        document.getElementById('paymentStep').style.display = 'none';
        document.getElementById('bookingForm').style.display = 'block';
    });
</script>