<?php
require_once __DIR__ . '/../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $baseUrl . "/index.php?page=login&status=error&message=Please login first.");
    exit;
}

// Load PayPal client ID for SDK
$paypalClientId = $env['PAYPAL_CLIENT_ID'] ?? "";

// Get slot ID from URL
$slotId = $_GET['id'] ?? null;
if (!$slotId) {
    header("Location: " . $baseUrl . "/index.php?page=home&status=error&message=Invalid slot.");
    exit;
}

// Fetch slot details
$stmt = $pdo->prepare("SELECT * FROM slots WHERE id = :id");
$stmt->execute(['id' => $slotId]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    header("Location: " . $baseUrl . "/index.php?page=home&status=error&message=Slot not found.");
    exit;
}

$isAvailable = $slot['available'] == 1;

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
        <form id="bookingForm" class="booking-form" method="post">
            <input type="hidden" name="slot_id" value="<?= $slot['id'] ?>">

            <div class="form-group">
                <label>Duration Type</label>
                <select id="duration_type" name="duration_type" required>
                    <?php if (count($availableTypes) > 1): ?>
                        <?php foreach ($availableTypes as $type): ?>
                            <option value="<?= $type ?>"><?= ucfirst($type) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Always show at least daily option, even if it's the only one -->
                        <option value="<?= $defaultType ?>"><?= ucfirst($defaultType) ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <?php if (in_array('hourly', $availableTypes)): ?>
            <!-- Hourly Fields -->
            <div id="hourlyFields" class="form-group hidden">
                <label>Booking Date</label>
                <input type="date" id="hourly_date" name="hourly_date" min="">

                <div class="time-selection-row">
                    <div class="time-field">
                        <label>Start Time</label>
                        <select id="start_time_hourly" name="start_time_hourly">
                            <option value="">Select Start Time</option>
                        </select>
                    </div>
                    <div class="time-field">
                        <label>End Time</label>
                        <select id="end_time_hourly" name="end_time_hourly">
                            <option value="">Select End Time</option>
                        </select>
                    </div>
                </div>
                <p id="hourlyDurationInfo" class="info"></p>
            </div>
            <?php endif; ?>

            <!-- Daily Fields - ALWAYS SHOWN for identical behavior -->
            <div id="dailyFields" class="form-group hidden">
                <label>Start Date</label>
                <input type="date" id="start_date" name="start_date" min="">

                <label>End Date</label>
                <input type="date" id="end_date" name="end_date" min="">
            </div>

            <?php if (in_array('monthly', $availableTypes)): ?>
            <!-- Monthly Fields -->
            <div id="monthlyFields" class="form-group hidden">
                <label>Start Date</label>
                <input type="date" id="start_date_monthly" name="start_date_monthly">
                <p id="monthlyEndDate" class="info">End Date: Select a start date</p>
            </div>
            <?php endif; ?>

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
                <button id="submitBtn" class="<?= $isAvailable ? 'paypal-btn' : 'secondary' ?>">
                    <?php if ($isAvailable): ?>
                        <i class="fab fa-paypal"></i>
                    <?php endif; ?>
                    <?= $isAvailable ? 'Pay with PayPal' : 'Join Waitlist' ?>
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
    // Format number with thousand separators and 2 decimal places
    function formatCurrency(amount) {
        return amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Time options for hourly booking (6 AM to 10 PM)
    const timeOptions = [];
    for (let hour = 6; hour <= 22; hour++) {
        const hour12 = hour > 12 ? hour - 12 : (hour === 0 ? 12 : hour);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const timeValue = hour.toString().padStart(2, '0') + ':00';
        const timeLabel = hour12 + ':00 ' + ampm;
        timeOptions.push({ value: timeValue, label: timeLabel, hour: hour });
    }

    // Populate start time dropdown
    function populateStartTimeOptions() {
        const startTimeSelect = document.getElementById('start_time_hourly');
        startTimeSelect.innerHTML = '<option value="">Select Start Time</option>';
        
        timeOptions.forEach(function(time) {
            // Exclude the last time slot as start time (need at least 1 hour)
            if (time.hour < 22) {
                const option = document.createElement('option');
                option.value = time.value;
                option.textContent = time.label;
                startTimeSelect.appendChild(option);
            }
        });
    }

    // Populate end time dropdown based on selected start time
    function populateEndTimeOptions() {
        const startTimeSelect = document.getElementById('start_time_hourly');
        const endTimeSelect = document.getElementById('end_time_hourly');
        const startValue = startTimeSelect.value;
        
        endTimeSelect.innerHTML = '<option value="">Select End Time</option>';
        
        if (!startValue) {
            endTimeSelect.disabled = true;
            return;
        }
        
        endTimeSelect.disabled = false;
        const startHour = parseInt(startValue.split(':')[0]);
        
        timeOptions.forEach(function(time) {
            // Only show times after the selected start time
            if (time.hour > startHour) {
                const option = document.createElement('option');
                option.value = time.value;
                option.textContent = time.label;
                endTimeSelect.appendChild(option);
            }
        });
    }

    // Update duration info display
    function updateHourlyDurationInfo() {
        const startTime = document.getElementById('start_time_hourly').value;
        const endTime = document.getElementById('end_time_hourly').value;
        const infoElement = document.getElementById('hourlyDurationInfo');
        
        if (startTime && endTime) {
            const startHour = parseInt(startTime.split(':')[0]);
            const endHour = parseInt(endTime.split(':')[0]);
            const hours = endHour - startHour;
            infoElement.textContent = 'Duration: ' + hours + ' hour' + (hours > 1 ? 's' : '');
        } else {
            infoElement.textContent = '';
        }
    }

    // Toggle form fields based on duration type
    function toggleFields() {
        const durationType = document.getElementById('duration_type').value;
        console.log('toggleFields called with durationType:', durationType);
        
        // Show/hide fields that exist - IDENTICAL BEHAVIOR FOR ALL SLOTS
        const hourlyFields = document.getElementById('hourlyFields');
        const dailyFields = document.getElementById('dailyFields');
        const monthlyFields = document.getElementById('monthlyFields');
        
        console.log('Fields exist - hourly:', !!hourlyFields, 'daily:', !!dailyFields, 'monthly:', !!monthlyFields);
        
        // Always show fields based on duration type - NO CONDITIONAL LOGIC BASED ON SLOT TYPE
        if (hourlyFields) {
            hourlyFields.style.display = durationType === 'hourly' ? 'block' : 'none';
        }
        if (dailyFields) {
            dailyFields.style.display = durationType === 'daily' ? 'block' : 'none';
        }
        if (monthlyFields) {
            monthlyFields.style.display = durationType === 'monthly' ? 'block' : 'none';
        }

        // Set required attributes - IDENTICAL FOR ALL SLOTS
        if (document.getElementById('hourly_date')) {
            document.getElementById('hourly_date').required = durationType === 'hourly';
        }
        if (document.getElementById('start_time_hourly')) {
            document.getElementById('start_time_hourly').required = durationType === 'hourly';
        }
        if (document.getElementById('end_time_hourly')) {
            document.getElementById('end_time_hourly').required = durationType === 'hourly';
        }
        if (document.getElementById('start_date')) {
            document.getElementById('start_date').required = durationType === 'daily';
        }
        if (document.getElementById('end_date')) {
            document.getElementById('end_date').required = durationType === 'daily';
        }
        if (document.getElementById('start_date_monthly')) {
            document.getElementById('start_date_monthly').required = durationType === 'monthly';
        }

        // Initialize fields when duration is selected - IDENTICAL FOR ALL SLOTS
        if (durationType === 'hourly' && hourlyFields) {
            populateStartTimeOptions();
            populateEndTimeOptions();
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('hourly_date').min = today;
        } else if (durationType === 'daily' && dailyFields) {
            // Set minimum date to today for daily booking - SAME FOR ALL SLOTS
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').min = today;
            document.getElementById('end_date').min = today;
        }

        console.log('Calling calculateCost');
        calculateCost();
    }

    // Calculate estimated cost - IDENTICAL LOGIC FOR ALL SLOTS
    function calculateCost() {
        const durationType = document.getElementById('duration_type').value;
        const vehicleType = document.getElementById('vehicle_type').value;
        let hours = 0;
        let days = 0;
        let months = 0;
        let actualDurationType = durationType;

        console.log('calculateCost called with durationType:', durationType);

        // Check if fields exist - DAILY FIELDS ALWAYS EXIST NOW
        const hourlyFieldsExist = document.getElementById('hourly_date') && document.getElementById('start_time_hourly') && document.getElementById('end_time_hourly');
        const dailyFieldsExist = document.getElementById('start_date') && document.getElementById('end_date'); // ALWAYS TRUE
        const monthlyFieldsExist = document.getElementById('start_date_monthly');

        console.log('Fields exist - hourly:', hourlyFieldsExist, 'daily:', dailyFieldsExist, 'monthly:', monthlyFieldsExist);

        if (durationType === 'hourly' && hourlyFieldsExist) {
            const hourlyDate = document.getElementById('hourly_date').value;
            const startTime = document.getElementById('start_time_hourly').value;
            const endTime = document.getElementById('end_time_hourly').value;

            if (hourlyDate && startTime && endTime) {
                const startHour = parseInt(startTime.split(':')[0]);
                const endHour = parseInt(endTime.split(':')[0]);
                hours = endHour - startHour;
            }

            if (document.getElementById('hourlyDurationInfo')) {
                updateHourlyDurationInfo();
            }
        } else if (durationType === 'daily' && dailyFieldsExist) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            console.log('Daily fields - startDate:', startDate, 'endDate:', endDate);
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1; // inclusive
                console.log('Days calculated:', days);
                
                // Check if monthly rate is available and worth switching to
                const monthlyRateAvailable = <?= $slot['monthly_rate'] ?> > 0;
                if (days >= 30 && monthlyRateAvailable) {
                    actualDurationType = 'monthly';
                    months = Math.ceil(days / 30);
                } else {
                    // For ALL slots, use daily calculation when daily is selected
                    actualDurationType = 'daily';
                    hours = days * 24; // Keep for potential hourly rate calculations if needed
                }
            }
        } else if (durationType === 'monthly' && monthlyFieldsExist) {
            const startDate = document.getElementById('start_date_monthly').value;
            if (startDate) {
                const start = new Date(startDate);
                const end = new Date(start);
                end.setDate(start.getDate() + 30);
                if (document.getElementById('monthlyEndDate')) {
                    document.getElementById('monthlyEndDate').textContent = 'End Date: ' + end.toISOString().slice(0, 10);
                }
                months = 1;
                hours = 24 * 30;
            }
        }

        if ((durationType === 'hourly' && hours > 0) ||
            (durationType === 'daily' && days > 0) ||
            (durationType === 'monthly' && months > 0)) {

            let baseCost = 0;
            let vehicleRate = 0;

            // Get base rate - IDENTICAL CALCULATION FOR ALL SLOTS
            if (actualDurationType === 'hourly') {
                baseCost = hours * <?= $slot['hourly_rate'] ?>;
            } else if (actualDurationType === 'daily') {
                baseCost = days * <?= $slot['daily_rate'] ?>;
            } else if (actualDurationType === 'monthly') {
                baseCost = months * <?= $slot['monthly_rate'] ?>;
            }

            // Get vehicle rate - IDENTICAL FOR ALL SLOTS
            const vehicleRates = {
                'motorcycle': <?= $slot['motorcycle_rate'] ?>,
                'car': <?= $slot['car_rate'] ?>,
                'suv': <?= $slot['suv_rate'] ?>,
                'van': <?= $slot['van_rate'] ?>,
                'truck': <?= $slot['truck_rate'] ?>,
                'mini_truck': <?= $slot['mini_truck_rate'] ?>
            };

            vehicleRate = vehicleRates[vehicleType] || 0;

            // Apply vehicle rate based on duration - IDENTICAL FOR ALL SLOTS
            if (actualDurationType === 'hourly') {
                vehicleRate *= hours;
            } else if (actualDurationType === 'daily') {
                vehicleRate *= days;
            } else if (actualDurationType === 'monthly') {
                vehicleRate *= months;
            }

            const totalCost = baseCost + vehicleRate;
            document.getElementById('estimatedCost').textContent = '₱' + formatCurrency(totalCost);

            // Enable/disable submit button based on cost - IDENTICAL FOR ALL SLOTS
            const submitBtn = document.getElementById('submitBtn');
            if (totalCost > 0) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Pay with PayPal';
            } else {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Select dates to estimate cost';
            }

            // Update duration type display if switched - IDENTICAL FOR ALL SLOTS
            if (actualDurationType !== durationType) {
                document.getElementById('duration_type').value = actualDurationType;
                toggleFields();
            }
        } else {
            document.getElementById('estimatedCost').textContent = '₱0.00';
        }
    }

    // Initial toggle - IDENTICAL FOR ALL SLOTS
    console.log('Initial toggleFields call');
    console.log('Default type:', '<?= $defaultType ?>');
    console.log('Available types:', <?= json_encode($availableTypes) ?>);
    toggleFields();

    // Event listeners - IDENTICAL FOR ALL SLOTS
    document.getElementById('duration_type').addEventListener('change', function() {
        toggleFields();
        calculateCost();
    });
    if (document.getElementById('hourly_date')) {
        document.getElementById('hourly_date').addEventListener('input', calculateCost);
    }
    if (document.getElementById('start_time_hourly')) {
        document.getElementById('start_time_hourly').addEventListener('change', function() {
            populateEndTimeOptions();
            // Reset end time when start time changes
            document.getElementById('end_time_hourly').value = '';
            calculateCost();
        });
    }
    if (document.getElementById('end_time_hourly')) {
        document.getElementById('end_time_hourly').addEventListener('change', calculateCost);
    }
    // DAILY FIELD EVENT LISTENERS - ALWAYS ATTACHED FOR CONSISTENT BEHAVIOR
    document.getElementById('start_date').addEventListener('input', calculateCost);
    document.getElementById('end_date').addEventListener('input', calculateCost);
    if (document.getElementById('start_date_monthly')) {
        document.getElementById('start_date_monthly').addEventListener('input', calculateCost);
    }
    document.getElementById('vehicle_type').addEventListener('change', calculateCost);

    // Form submission - IDENTICAL VALIDATION FOR ALL SLOTS
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

        // Validate form - SAME LOGIC FOR ALL SLOTS
        const durationType = document.getElementById('duration_type').value;
        let isValid = true;

        if (durationType === 'hourly') {
            const hourlyDate = document.getElementById('hourly_date') ? document.getElementById('hourly_date').value : '';
            const startTime = document.getElementById('start_time_hourly') ? document.getElementById('start_time_hourly').value : '';
            const endTime = document.getElementById('end_time_hourly') ? document.getElementById('end_time_hourly').value : '';

            if (!hourlyDate) {
                alert('Please select a booking date for hourly booking.');
                isValid = false;
            } else if (!startTime) {
                alert('Please select a start time for hourly booking.');
                isValid = false;
            } else if (!endTime) {
                alert('Please select an end time for hourly booking.');
                isValid = false;
            }
        } else if (durationType === 'daily') {
            // DAILY VALIDATION - IDENTICAL FOR ALL SLOTS
            const start = document.getElementById('start_date').value;
            const end = document.getElementById('end_date').value;
            if (!start || !end || new Date(start) > new Date(end)) {
                alert('Please select valid start and end dates for daily booking.');
                isValid = false;
            }
        } else if (durationType === 'monthly') {
            const start = document.getElementById('start_date_monthly') ? document.getElementById('start_date_monthly').value : '';
            if (!start) {
                alert('Please select a start date for monthly booking.');
                isValid = false;
            }
        }

        if (!isValid) return;

        // Create booking first - SAME FOR ALL SLOTS
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating Booking...';

        const formData = new FormData(this);
        const durationTypeVal = document.getElementById('duration_type').value;

        // Generate start_time and end_time based on inputs - IDENTICAL FOR ALL SLOTS
        let startTime, endTime;
        if (durationTypeVal === 'hourly') {
            const hourlyDate = document.getElementById('hourly_date') ? document.getElementById('hourly_date').value : '';
            const startTimeVal = document.getElementById('start_time_hourly') ? document.getElementById('start_time_hourly').value : '';
            const endTimeVal = document.getElementById('end_time_hourly') ? document.getElementById('end_time_hourly').value : '';
            startTime = hourlyDate + ' ' + startTimeVal + ':00';
            endTime = hourlyDate + ' ' + endTimeVal + ':00';
        } else if (durationTypeVal === 'daily') {
            // DAILY BOOKING - IDENTICAL FOR ALL SLOTS
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            startTime = startDate + ' 00:00:00';
            endTime = endDate + ' 23:59:59';
        } else if (durationTypeVal === 'monthly') {
            const startDate = document.getElementById('start_date_monthly') ? document.getElementById('start_date_monthly').value : '';
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
                console.log('Booking API response:', data);
                if (data.success) {
                    if (data.booking_id) {
                        // Store booking ID
                        document.getElementById('bookingId').value = data.booking_id;
                        console.log('Booking ID stored:', data.booking_id);

                        // Show payment step
                        const bookingForm = document.getElementById('bookingForm');
                        const paymentStep = document.getElementById('paymentStep');
                        console.log('Forms found - bookingForm:', !!bookingForm, 'paymentStep:', !!paymentStep);
                        
                        if (bookingForm) bookingForm.style.display = 'none';
                        if (paymentStep) paymentStep.style.display = 'block';

                        // Update payment amount
                        const amountElement = document.getElementById('estimatedCost');
                        const amount = amountElement ? amountElement.textContent.replace('₱', '').replace(/,/g, '') : '0';
                        console.log('Amount to pay:', amount);
                        
                        const paymentAmountElement = document.getElementById('paymentAmount');
                        if (paymentAmountElement) paymentAmountElement.textContent = amount;

                        // Initialize PayPal button
                        console.log('Initializing PayPal button with booking ID:', data.booking_id, 'Amount:', amount);
                        initPayPalButton(data.booking_id, amount);
                    } else {
                        // Waitlist
                        alert(data.message);
                        window.location.href = '<?php echo $baseUrl; ?>/index.php?page=slots';
                    }
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
    } else {
        console.error('Booking form not found!');
    }


    // Back to Form - IDENTICAL FOR ALL SLOTS
    document.getElementById('backToFormBtn').addEventListener('click', function() {
        document.getElementById('paymentStep').style.display = 'none';
        document.getElementById('bookingForm').style.display = 'block';
    });

    // PayPal Integration - IDENTICAL FOR ALL SLOTS
    function initPayPalButton(bookingId, amount) {
        console.log('PayPal function called. PayPal defined:', typeof paypal !== 'undefined');
        console.log('Booking ID:', bookingId, 'Amount:', amount);
        
        const paypalContainer = document.getElementById('paypal-button-container');
        console.log('PayPal container found:', !!paypalContainer);
        
        if (typeof paypal === 'undefined') {
            alert('PayPal SDK not loaded. Please refresh the page and try again.');
            return;
        }
        
        if (!paypalContainer) {
            alert('PayPal button container not found!');
            return;
        }
        
        // Clear existing buttons
        paypalContainer.innerHTML = '';
        console.log('Creating PayPal button...');
        
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color:  'gold',
                shape:  'rect',
                label:  'paypal'
            },
            createOrder: function(data, actions) {
                console.log('Creating PayPal order...');
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
                        console.log('Order data:', orderData);
                        if (orderData.success) {
                            return orderData.order_id;
                        } else {
                            throw new Error(orderData.message);
                        }
                    });
            },
            onApprove: function(data, actions) {
                console.log('Payment approved, capturing order...');
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
                        console.log('Capture data:', captureData);
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
            },
            onCancel: function(data) {
                console.log('Payment cancelled by user');
                alert('Payment was cancelled. You can return to the booking form.');
            }
        }).render('#paypal-button-container');
        console.log('PayPal button rendered');
    }
</script>

<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($paypalClientId); ?>&currency=PHP&components=buttons"></script>