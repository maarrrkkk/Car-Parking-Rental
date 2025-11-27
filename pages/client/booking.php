<?php
require_once __DIR__ . '/../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $baseUrl . "/index.php?page=login&status=error&message=Please login first.");
    exit;
}

// Get slot ID from URL
$slotId = $_GET['id'] ?? null;
if (!$slotId) {
    header("Location: " . $baseUrl . "/index.php?page=home&status=error&message=Invalid slot.");
    exit;
}

// Fetch slot details
$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = :id AND available = 1");
$stmt->execute(['id' => $slotId]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    header("Location: " . $baseUrl . "/index.php?page=home&status=error&message=Slot not available.");
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


<section class="booking-wrapper ">
    <div class="booking-container mt-5">

        <!-- Header -->
        <div class="booking-header">
            <h2><?= htmlspecialchars($slot['name']) ?></h2>
        </div>

        <!-- Slot Details -->
        <div class="slot-details">
            <div class="slot-image">
                <img src="<?= htmlspecialchars($slot['image'] ?? 'assets/images/parking_slots/default.jpg') ?>" alt="Slot Image">
            </div>

            <div class="slot-info">
                <h3>Rates</h3>
                <?php if ($slot['hourly_rate'] > 0): ?>
                    <p>Hourly Rate: ₱<?= number_format($slot['hourly_rate'], 2) ?></p>
                <?php endif; ?>

                <?php if ($slot['daily_rate'] > 0): ?>
                    <p>Daily Rate: ₱<?= number_format($slot['daily_rate'], 2) ?></p>
                <?php endif; ?>

                <?php if ($slot['monthly_rate'] > 0): ?>
                    <p>Monthly Rate: ₱<?= number_format($slot['monthly_rate'], 2) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Booking Form -->
        <form id="bookingForm" class="booking-form">
            <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">

            <?php if (count($availableTypes) > 1): ?>
                <div class="form-group">
                    <label>Duration Type</label>
                    <select id="duration_type" name="duration_type" required>
                        <?php foreach ($availableTypes as $type): ?>
                            <option value="<?= $type ?>"><?= ucfirst($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" id="duration_type" name="duration_type" value="<?= $defaultType ?>">
            <?php endif; ?>

            <!-- Hourly Fields -->
            <div id="hourlyFields" class="form-group hidden">
                <label>Start Time</label>
                <input type="datetime-local" id="start_time_hourly" name="start_time_hourly">

                <label>End Time</label>
                <input type="datetime-local" id="end_time_hourly" name="end_time_hourly">
            </div>

            <!-- Daily Fields -->
            <div id="dailyFields" class="form-group hidden">
                <label>Start Date</label>
                <input type="date" id="start_date" name="start_date">

                <label>End Date</label>
                <input type="date" id="end_date" name="end_date">
            </div>

            <!-- Monthly Fields -->
            <div id="monthlyFields" class="form-group hidden">
                <label>Start Date</label>
                <input type="date" id="start_date_monthly" name="start_date_monthly">
                <p id="monthlyEndDate" class="info">End Date: Select a start date</p>
            </div>

            <div class="form-group">
                <label>Vehicle Type</label>
                <select id="vehicle_type" name="vehicle_type" required>
                    <option value="motorcycle">Motorcycle (+₱<?= number_format($slot['motorcycle_rate'], 2) ?>)</option>
                    <option value="car">Car (+₱<?= number_format($slot['car_rate'], 2) ?>)</option>
                    <option value="suv">SUV (+₱<?= number_format($slot['suv_rate'], 2) ?>)</option>
                    <option value="van">Van (+₱<?= number_format($slot['van_rate'], 2) ?>)</option>
                    <option value="truck">Truck (+₱<?= number_format($slot['truck_rate'], 2) ?>)</option>
                    <option value="mini_truck">Mini Truck (+₱<?= number_format($slot['mini_truck_rate'], 2) ?>)</option>
                </select>
            </div>

            <p class="estimated text-danger">Estimated Cost: <span id="estimatedCost">₱0.00</span></p>

            <div class="button-row">
                <button id="submitBtn" class="paypal-btn">
                    <i class="fab fa-paypal"></i>
                    Pay with PayPal
                </button>

                <a href="<?= $baseUrl ?>/index.php?page=home" class="secondary">Cancel</a>
            </div>
        </form>

        <!-- Payment Section -->
        <div id="paymentStep" class="payment-section hidden">
            <input type="hidden" id="bookingId">

            <h3>Complete Payment</h3>
            <p class="pay-amount">Amount: ₱<span id="paymentAmount">0.00</span></p>

            <div id="paypal-button-container"></div>

            <button id="backToFormBtn" class="secondary mt-3">Back</button>
        </div>
    </div>
</section>


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

        fetch('<?php echo $baseUrl; ?>/api/booking.php', {
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

                    // Initialize PayPal button
                    initPayPalButton(data.booking_id, amount);
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


    // Back to Form
    document.getElementById('backToFormBtn').addEventListener('click', function() {
        document.getElementById('paymentStep').style.display = 'none';
        document.getElementById('bookingForm').style.display = 'block';
    });

    // PayPal Integration
    function initPayPalButton(bookingId, amount) {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch('<?php echo $baseUrl; ?>/api/payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=create_order&booking_id=' + bookingId
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(orderData) {
                        if (orderData.success) {
                            return orderData.order_id;
                        } else {
                            throw new Error(orderData.message);
                        }
                    });
            },
            onApprove: function(data, actions) {
                return fetch('<?php echo $baseUrl; ?>/api/payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=capture_order&order_id=' + data.orderID + '&booking_id=' + bookingId
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(captureData) {
                        if (captureData.success) {
                            alert('Payment completed successfully! Your booking is now active.');
                            window.location.href = '<?php echo $baseUrl; ?>/index.php?page=profile';
                        } else {
                            alert('Payment failed: ' + captureData.message);
                        }
                    });
            },
            onError: function(err) {
                console.error('PayPal error:', err);
                alert('An error occurred with PayPal. Please try again.');
            }
        }).render('#paypal-button-container');
    }
</script>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypalClientId); ?>&currency=PHP&components=buttons"></script>