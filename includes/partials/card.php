<?php
require_once __DIR__ . '/../../config/database.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;

// Fetch slots
$limit = $limit ?? 0; // Default to all for other pages
$query = "SELECT * FROM slots ORDER BY id ASC";
if ($limit > 0) {
    $query .= " LIMIT $limit";
}
$stmt = $pdo->query($query);
$parkingSpaces = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's active bookings if logged in
$userActiveBookings = [];
if ($isLoggedIn && $userId) {
    $stmt = $pdo->prepare("SELECT slot_id FROM bookings WHERE user_id = :user_id AND status = 'active'");
    $stmt->execute(['user_id' => $userId]);
    $userActiveBookings = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Create a lookup array for faster checking
$userActiveBookingSlots = array_flip($userActiveBookings);

// Check for any active bookings on slots (regardless of available column) with end times
$stmt = $pdo->prepare("SELECT slot_id, end_time FROM bookings WHERE status = 'active' ORDER BY end_time ASC");
$stmt->execute();
$activeBookingsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$slotsWithActiveBookings = [];
$slotEndTimes = [];
foreach ($activeBookingsData as $booking) {
    $slotId = $booking['slot_id'];
    if (!isset($slotsWithActiveBookings[$slotId])) {
        $slotsWithActiveBookings[$slotId] = true;
        $slotEndTimes[$slotId] = $booking['end_time'];
    }
}
?>

<div class="row g-4">
    <?php if (empty($parkingSpaces)): ?>
        <!-- No Slots Available Card -->
        <div class="col-12">
            <article class="card h-100 shadow-sm text-center p-5 d-flex align-items-center justify-content-center">
                <div>
                    <h4 class="mt-3 text-muted">No Parking Slots Available</h4>
                    <p class="text-secondary">Please check back later.</p>
                </div>
            </article>
        </div>

    <?php else: ?>
        <?php foreach ($parkingSpaces as $space): ?>
            <div class="col-6 col-md-4">
                <a href="?page=slot_details&id=<?= $space['id'] ?>" class="text-decoration-none d-block h-100">

                    <article class="card h-100 shadow-sm">

                        <!-- Hero Section -->
                        <section class="card__hero"
                            style="background: url('<?= htmlspecialchars($space['image'] ?? 'assets/images/default.jpg') ?>') center/cover no-repeat;">

                            <header class="card__hero-header d-flex justify-content-between align-items-center text-white p-2 bg-dark bg-opacity-50 rounded">
                                <span>
                                    ₱<?= number_format($space['hourly_rate'], 2) ?>/hr |
                                    ₱<?= number_format($space['daily_rate'], 2) ?>/day |
                                    ₱<?= number_format($space['monthly_rate'], 2) ?>/mon
                                </span>
                            </header>

                            <p class="card__job-title">
                                <?= htmlspecialchars($space['name']) ?>
                            </p>
                        </section>

                        <!-- Card Content -->
                        <div class="margin-top-auto p-3 gap-2 d-flex flex-column">
                            <?php
                            // Determine button text based on user booking status
                            $buttonText = '';
                            $buttonClass = 'card__btn2';
                            $buttonDisabled = '';
                            $onclickAction = "handleBooking({$space['id']}, " . ($isLoggedIn ? 'true' : 'false') . ")";
                            
                            if ($isLoggedIn && isset($userActiveBookingSlots[$space['id']])) {
                                // User has an active booking for this slot
                                $buttonText = 'Currently Using';
                                $buttonClass = 'card__btn2 btn-success';
                                $buttonDisabled = 'disabled';
                                $onclickAction = '';
                            } elseif (isset($slotsWithActiveBookings[$space['id']])) {
                                // Slot has active bookings (by other users) - show waitlist with end time
                                $endTime = $slotEndTimes[$space['id']] ?? null;
                                if ($endTime) {
                                    $buttonText = 'Join Waitlist (Available: ' . date('M d, h:i A', strtotime($endTime)) . ')';
                                } else {
                                    $buttonText = 'Join Waitlist';
                                }
                            } elseif ($space['available']) {
                                // Slot is available and user doesn't have active booking
                                $buttonText = 'Book This';
                            } else {
                                // Slot is marked unavailable but no active bookings - show waitlist
                                $buttonText = 'Join Waitlist';
                            }
                            ?>
                            <button
                                class="<?= $buttonClass ?>"
                                <?= $buttonDisabled ?>
                                onclick="<?= $onclickAction ?>">
                                <?= $buttonText ?>
                            </button>
                        </div>

                    </article>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

