<?php
$contactInfo = [
    [
        'icon' => 'fas fa-phone',
        'title' => 'Phone',
        'info' => '+63 917 123 4567',
        'description' => 'Call us for immediate assistance'
    ],
    [
        'icon' => 'fas fa-envelope',
        'title' => 'Email',
        'info' => 'support@carparkingcentennial.com',
        'description' => 'Send us an email anytime'
    ],
    [
        'icon' => 'fas fa-map-marker-alt',
        'title' => 'Address',
        'info' => 'Centennial City, Philippines',
        'description' => 'Local parking services'
    ],
    [
        'icon' => 'fas fa-clock',
        'title' => 'Business Hours',
        'info' => '24/7 Customer Support',
        'description' => 'We\'re always here to help'
    ]
];

$faqItems = [
    [
        'question' => 'How do I cancel a reservation?',
        'answer' => 'You can cancel your reservation up to 1 hour before your scheduled time through our app or website.'
    ],
    [
        'question' => 'Is my vehicle insured while parked?',
        'answer' => 'While we provide secure facilities, we recommend checking with your auto insurance provider for coverage details.'
    ],
    [
        'question' => 'Do you offer monthly parking passes?',
        'answer' => 'Yes! We offer discounted monthly and annual parking passes for frequent users. Contact us for more details.'
    ]
];

// No longer needed, using AJAX
?>

<section class="py-5 bg-light">
    <div class="mt-3">
        <!-- Header -->
        <section class="contact-header">
            <div class="container">
                <div class="section-header">
                    <h1 class="section-title">Get in Touch</h1>
                    <p class="section-subtitle">Have questions about parking reservations or need assistance? We're here to help you 24/7.</p>
                </div>
            </div>
        </section>

        <!-- Contact Info and Form -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <!-- Contact Information -->
                    <div class="contact-info">
                        <h2 class="info-title">Contact Information</h2>
                        <div class="info-grid">
                            <?php foreach ($contactInfo as $item): ?>
                                <div class="info-card">
                                    <div class="info-content">
                                        <div class="info-item">
                                            <i class="<?php echo $item['icon']; ?> info-icon"></i>
                                            <div class="info-details">
                                                <h3 class="info-label"><?php echo htmlspecialchars($item['title']); ?></h3>
                                                <p class="info-value"><?php echo htmlspecialchars($item['info']); ?></p>
                                                <p class="info-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="emergency-card">
                            <div class="card-header">
                                <h3 class="card-title">Emergency Assistance</h3>
                                <p class="card-description">If you're locked out or need immediate help at a parking location</p>
                            </div>
                            <div class="card-content">
                                <div class="emergency-contact">
                                    <i class="fas fa-phone emergency-icon"></i>
                                    <span class="emergency-number">+63 917 911 PARK</span>
                                </div>
                                <p class="emergency-description">Available 24/7 for parking emergencies</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-form-section">
                        <div class="form-card">
                            <div class="card-header">
                                <h3 class="card-title">Send us a Message</h3>
                                <p class="card-description">Fill out the form below and we'll get back to you within 24 hours</p>
                            </div>
                            <div class="card-content">
                                <form method="POST" class="contact-form" id="contactForm">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" id="name" name="name" class="form-input" placeholder="Your full name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-input" placeholder="your.email@example.com" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject" class="form-label">Subject</label>
                                        <input type="text" id="subject" name="subject" class="form-input" placeholder="What's this about?" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea id="message" name="message" class="form-textarea" rows="5" placeholder="Tell us how we can help you..." required></textarea>
                                    </div>
                                    <button type="submit" name="submit_contact" class="btn btn-primary btn-full">Send Message</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Frequently Asked Questions</h2>
                    <p class="section-subtitle">Quick answers to common questions</p>
                </div>
                <div class="faq-grid">
                    <?php foreach ($faqItems as $item): ?>
                        <div class="faq-card">
                            <div class="card-header">
                                <h3 class="faq-question"><?php echo htmlspecialchars($item['question']); ?></h3>
                            </div>
                            <div class="card-content">
                                <p class="faq-answer"><?php echo htmlspecialchars($item['answer']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2 class="cta-title">Still Have Questions?</h2>
                    <p class="cta-subtitle">Don't hesitate to reach out. Our customer support team is standing by to help.</p>
                    <div class="cta-buttons">
                        <button class="btn btn-primary btn-lg">Call Now: +63 917 123 4567</button>
                        <button class="btn btn-outline btn-lg text-dark">Live Chat Support</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        // Remove any existing alerts
        const existingAlert = this.querySelector('.alert');
        if (existingAlert) existingAlert.remove();

        fetch('api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertDiv = document.createElement('div');
                alertDiv.className = data.success ? 'alert alert-success' : 'alert alert-error';
                alertDiv.innerHTML = `<i class="fas fa-${data.success ? 'check-circle' : 'exclamation-circle'}"></i> ${data.message}`;
                this.insertBefore(alertDiv, this.firstElementChild);

                if (data.success) {
                    this.reset();
                }
            })
            .catch(error => {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-error';
                alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.';
                this.insertBefore(alertDiv, this.firstElementChild);
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
    });
</script>