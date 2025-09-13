<?php
$parkingSpaces = [
    [
        'id' => '1',
        'location' => 'Slot A1',
        'hourlyRate' => 10,
        'dailyRate' => 50,
        'available' => true,
        'image' => 'assets/images/parking1.jpg',
        'features' => ['24/7 Security', 'CCTV Monitoring', 'Covered Parking']
    ],
    [
        'id' => '2',
        'location' => 'Slot A2',
        'hourlyRate' => 10,
        'dailyRate' => 50,
        'available' => false,
        'image' => 'assets/images/parking2.jpg',
        'features' => ['24/7 Security', 'CCTV Monitoring', 'Covered Parking']
    ]
];
?>

<section class="position-relative text-white">
<!-- Background Image -->
  <img src="assets/images/centennial-parking.png" 
       class="w-100 vh-100 object-fit-cover hero-background-image" 
       alt="Centennial Parking">

  <!-- Black Overlay -->
  <div class="position-absolute top-0 start-0 w-100 h-100 bg-black" style="opacity: 0.6;"></div>

  <!-- Content on top -->
  <div class="w-100 position-absolute top-50 start-50 translate-middle text-white container">
    <h1 class="display-3 fw-bold">No Parking Space Near City?</h1>
    <p class="lead">Reserve your spot now at Centennial Parking, Calbayog City. Safe, convenient, and affordable.</p>
    <a href="#" class="btn btn-primary btn-md">Book Parking Now</a>
    <a href="?page=about" class="btn btn-outline-light btn-md">Learn More</a>
  </div>
</section>

<!-- Available Parking Space Section -->
<section class="py-5 bg-light">
    <div class="container">

    <?php include 'pages/client/components/card.php'; ?>

    </div>
</section>


<!-- Featured section -->
<section class="py-5 bg-white">
    <div class="container text-center">
        <h2 class="mb-5">Why Choose Car Parking Rental, Calbayog City?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4">
                    <i class="fas fa-map-marker-alt fa-2x mb-3 text-primary"></i>
                    <h5>Central Location</h5>
                    <p>Easily accessible within Calbayog City.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="fas fa-clock fa-2x mb-3 text-primary"></i>
                    <h5>24/7 Availability</h5>
                    <p>Book and access your parking space anytime.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="fas fa-car fa-2x mb-3 text-primary"></i>
                    <h5>Safe & Secure</h5>
                    <p>Security cameras and guards ensure your vehicle is protected.</p>
                </div>
            </div>
        </div>
    </div>
</section>