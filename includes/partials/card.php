<?php
require_once __DIR__ . '/../../config/database.php';

$isLoggedIn = isset($_SESSION['user_id']);

// Fetch slots
$limit = $limit ?? 0; // Default to all for other pages
$query = "SELECT * FROM slots ORDER BY id ASC";
if ($limit > 0) {
    $query .= " LIMIT $limit";
}
$stmt = $pdo->query($query);
$parkingSpaces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row g-4">
    <?php if (empty($parkingSpaces)): ?>
        <!-- No Slots Available Card -->
        <div class="col-12">
            <article class="card h-100 shadow-sm text-center p-5 d-flex align-items-center justify-content-center">
                <div>
                    <!-- <img src="assets/images/no-slot.png" alt="No Slot" style="width:100px; opacity:0.7;"> -->
                    <h4 class="mt-3 text-muted">No Parking Slots Available</h4>
                    <p class="text-secondary">Please check back later.</p>
                </div>
            </article>
        </div>
    <?php else: ?>
        <?php foreach ($parkingSpaces as $space): ?>
            <div class="col-md-4">
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

                        <p class="card__job-title"><?= htmlspecialchars($space['name']) ?></p>
                    </section>

                    <!-- Card Content -->
                    <div class="margin-top-auto p-3 gap-2 d-flex flex-column">
                        <!-- View Details -->
                        <a href="?page=slot_details&id=<?= $space['id'] ?>" class="btn btn-outline-primary w-100 mb-2">
                            View Details
                        </a>

                        <!-- Book Now -->
                        <button
                            class="card__btn2 <?= $space['available'] ? '' : 'disabled' ?>"
                            onclick="handleBooking(<?= $space['id'] ?>, <?= $isLoggedIn ? 'true' : 'false' ?>)">
                            <?= $space['available'] ? 'Book This' : 'Unavailable' ?>
                        </button>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
