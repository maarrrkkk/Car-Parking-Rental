<div class="row g-4">
    <?php foreach ($parkingSpaces as $space): ?>
        <div class="col-md-4">
            <article class="card h-100 shadow-sm">
                <!-- Hero Section -->
                <section class="card__hero">
                    <header class="card__hero-header d-flex justify-content-between align-items-center">
                        <span>₱<?= htmlspecialchars($space['hourlyRate']) ?>/hr | ₱<?= htmlspecialchars($space['dailyRate']) ?>/day</span>
                    </header>

                    <p class="card__job-title"><?= htmlspecialchars($space['location']) ?></p>
                </section>

                <!-- Card Content (replaces footer) -->
                <div class="margin-top-auto p-3 gap-2 d-flex flex-column">


                    <button class="card__btn2_stroke">
                        View Details
                    </button>   
                    <button class="card__btn2 <?= $space['available'] ? '' : 'disabled' ?>">
                        <?= $space['available'] ? 'Book Now' : 'Waitlist' ?>
                    </button>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>