<!-- Hero Section -->
<section class="hero-section position-relative">

    <!-- Optional Background Image -->
    <div class="hero-background">
        <img src="assets/images/parking-bg.jpg" class="hero-image" alt="">
    </div>

    <div class="hero-overlay"></div>

    <!-- Content Layer -->
    <div class="hero-content-wrapper position-absolute z-3 top-50 start-50 translate-middle w-100 px-4">
        <div class="container">
            <div class="row">

                <!-- Text -->
                <div class="col-12 col-md-8 col-lg-6 text-center text-white text-md-start">
                    <h1 class="hero-title animate__animated animate__fadeInUp">
                        No Parking Space Near City?
                    </h1>

                    <p class="hero-subtitle mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        Reserve your spot now at <strong>Centennial Parking</strong>, Calbayog City.  
                        Safe, convenient, and affordable parking solutions.
                    </p>

                    <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-2s">
                        <a href="?page=about" class="btn btn-light text-dark fw-semibold">
                            <i class="fas fa-info-circle me-2"></i> Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="scroll-indicator position-absolute bottom-0 start-50 z-3 translate-middle-x mb-4">
        <i class="fas fa-chevron-down fs-3"></i>
    </div>

</section>

<!-- Features Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="section-title display-5 fw-bold mb-3">Why Choose Us?</h2>
                <p class="section-subtitle lead text-muted">
                    Experience the best parking service in Calbayog City
                </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Prime Location</h5>
                        <p class="card-text text-muted">
                            Strategically located in the heart of Calbayog City for maximum convenience and accessibility.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Secure & Safe</h5>
                        <p class="card-text text-muted">
                            24/7 security surveillance with trained personnel ensuring your vehicle's safety at all times.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">24/7 Access</h5>
                        <p class="card-text text-muted">
                            Round-the-clock access to your parking space with flexible booking options.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Parking Spaces Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <h2 class="section-title display-5 fw-bold mb-3">Available Parking Spaces</h2>
                <p class="section-subtitle lead text-muted">
                    Choose from our premium parking locations in Calbayog City
                </p>
            </div>
        </div>
        <?php $limit = 4; ?>
        <?php include 'includes/partials/card.php'; ?>
        <div class="text-center mt-4">
            <a href="?page=slots" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-list me-2"></i>View All Spaces
            </a>
        </div>
    </div>
</section>

<!-- Stats Section
<section class="py-5 text-white bg-primary bg-opacity-70">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold mb-2">500+</h3>
                    <p class="mb-0">Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold mb-2">50+</h3>
                    <p class="mb-0">Parking Spaces</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold mb-2">24/7</h3>
                    <p class="mb-0">Customer Support</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold mb-2">5★</h3>
                    <p class="mb-0">Average Rating</p>
                </div>
            </div>
        </div>
    </div>
</section> -->

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="display-5 fw-bold mb-4">Ready to Park Smart?</h2>
                <p class="lead text-muted mb-4">
                    Join thousands of satisfied customers who trust Centennial Parking for their vehicle needs.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="?page=register" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                    <a href="?page=contact" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-phone me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>