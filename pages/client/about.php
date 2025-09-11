<?php
$stats = [
    ['label' => 'Active Users', 'value' => '50,000+', 'icon' => 'fas fa-users'],
    ['label' => 'Parking Locations', 'value' => '200+', 'icon' => 'fas fa-clock'],
    ['label' => 'Years of Service', 'value' => '8', 'icon' => 'fas fa-award'],
    ['label' => 'Security Rating', 'value' => 'A+', 'icon' => 'fas fa-shield-alt']
];

$teamMembers = [
    [
        'name' => 'Sarah Johnson',
        'role' => 'CEO & Founder',
        'bio' => 'Former urban planning specialist with 15 years experience in city infrastructure.'
    ],
    [
        'name' => 'Michael Chen',
        'role' => 'CTO',
        'bio' => 'Technology leader specializing in smart city solutions and mobile applications.'
    ],
    [
        'name' => 'Emily Rodriguez',
        'role' => 'Head of Operations',
        'bio' => 'Operations expert ensuring smooth parking experiences across all locations.'
    ]
];
?>

<!-- Hero Section -->
<!-- About Hero Section -->
<section class="position-relative bg-dark text-white py-5">
  <!-- Background image -->
  <div class="position-absolute top-0 start-0 w-100 h-100">
    <img src="assets/images/about-bg.jpg" 
         class="w-100 h-100 object-fit-cover" 
         alt="About ParkEase">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5;"></div>
  </div>

  <!-- Content -->
  <div class="container position-relative text-center py-5">
    <h1 class="display-3 fw-bold mb-3">About Car Parking Rental</h1>
    <p class="lead fs-4">Revolutionizing urban parking since 2016</p>
  </div>
</section>


<!-- Company Story -->
<section class="story-section">
    <div class="container">
        <div class="story-grid">
            <div class="story-content">
                <h2 class="section-title">Our Story</h2>
                <div class="story-text">
                    <p>ParkEase was founded in 2016 with a simple mission: to eliminate the stress and frustration of finding parking in busy urban areas. Our founders, experienced in urban planning and technology, recognized that parking was one of the biggest pain points for city dwellers and visitors alike.</p>
                    
                    <p>What started as a small network of 10 parking locations has grown into a comprehensive platform serving over 50,000 users across 200+ premium parking facilities. We've partnered with property owners, businesses, and municipalities to create a seamless parking experience that benefits everyone.</p>
                    
                    <p>Today, ParkEase continues to innovate in the smart parking space, integrating cutting-edge technology with human-centered design to make urban mobility more efficient and accessible.</p>
                </div>
            </div>
            <div class="stats-grid">
                <?php foreach ($stats as $stat): ?>
                <div class="stat-card">
                    <div class="stat-content">
                        <i class="<?php echo $stat['icon']; ?> stat-icon"></i>
                        <div class="stat-value"><?php echo htmlspecialchars($stat['value']); ?></div>
                        <p class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Values -->
<section class="values-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our Mission & Values</h2>
            <p class="section-subtitle">We're committed to creating smarter, more sustainable urban environments through innovative parking solutions.</p>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="card-header">
                    <i class="fas fa-shield-alt value-icon"></i>
                    <h3 class="card-title">Security First</h3>
                </div>
                <div class="card-content">
                    <p>Every parking location is thoroughly vetted and equipped with security measures to ensure your vehicle's safety.</p>
                </div>
            </div>
            <div class="value-card">
                <div class="card-header">
                    <i class="fas fa-users value-icon"></i>
                    <h3 class="card-title">Customer Focused</h3>
                </div>
                <div class="card-content">
                    <p>We design every feature and service with our users in mind, constantly gathering feedback to improve the experience.</p>
                </div>
            </div>
            <div class="value-card">
                <div class="card-header">
                    <i class="fas fa-clock value-icon"></i>
                    <h3 class="card-title">Reliability</h3>
                </div>
                <div class="card-content">
                    <p>Our platform and partner locations maintain 99.9% uptime, ensuring you can always count on us when you need parking.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-subtitle">The passionate people behind ParkEase</p>
        </div>
        <div class="team-grid">
            <?php foreach ($teamMembers as $member): ?>
            <div class="team-card">
                <div class="card-header team-header">
                    <div class="team-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="card-title"><?php echo htmlspecialchars($member['name']); ?></h3>
                    <span class="badge badge-secondary"><?php echo htmlspecialchars($member['role']); ?></span>
                </div>
                <div class="card-content">
                    <p class="team-bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>