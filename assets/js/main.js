// Main JavaScript functionality for Car Parking Rental

// Wait for the DOM to be fully loaded

document.addEventListener('DOMContentLoaded', function() {
    // Initialize page functionality
    initializeNavigation();
    initializeButtons();
    initializeForms();
});

// Navigation functionality
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Let the default navigation happen
            // This is just for any additional handling if needed
        });
    });
}

// Mapbox integration
// Replace with your own Mapbox token
mapboxgl.accessToken = 'YOUR_MAPBOX_ACCESS_TOKEN';

// Initialize map
const map = new mapboxgl.Map({
container: 'map', // ID of the div
style: 'mapbox://styles/mapbox/streets-v11', // Map style
center: [124.595379, 12.067281], // [longitude, latitude] of Calbayog
zoom: 14 // Zoom level
});

// Add zoom & rotation controls
map.addControl(new mapboxgl.NavigationControl());

// Add a marker for Centennial Parking
new mapboxgl.Marker({ color: "red" })
.setLngLat([124.595379, 12.067281]) // Same coords
.setPopup(new mapboxgl.Popup().setHTML("<b>Centennial Parking</b><br>Calbayog City"))
.addTo(map);


// // Parking search functionality
// function filterParking() {
//     const input = document.getElementById("parkingSearch").value.toLowerCase();
//     const cards = document.querySelectorAll(".parking-card");
//     cards.forEach(card => {
//         const text = card.innerText.toLowerCase();
//         card.style.display = text.includes(input) ? "block" : "none";
//     });
// }

// function applySuggestion(term) {
//     document.getElementById("parkingSearch").value = term;
//     filterParking();
// }

// navigation Toggle menu on mobile
document.getElementById("nav-toggle").addEventListener("click", function () {
  document.getElementById("nav-links").classList.toggle("show");
});

// Button interactions
function initializeButtons() {
    // Parking booking buttons
    const bookingButtons = document.querySelectorAll('.parking-card .btn');
    
    bookingButtons.forEach(button => {
        if (!button.disabled) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const cardTitle = this.closest('.parking-card').querySelector('.card-title').textContent;
                
                if (this.textContent.trim() === 'Book Now') {
                    showNotification(`Booking initiated for ${cardTitle}`, 'success');
                    // Here you would typically redirect to a booking page or open a modal
                } else if (this.textContent.trim() === 'Waitlist') {
                    showNotification(`Added to waitlist for ${cardTitle}`, 'info');
                }
            });
        }
    });
    
    // Hero section buttons
    const heroButtons = document.querySelectorAll('.hero-buttons .btn');
    
    heroButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.textContent.includes('Find Parking')) {
                // Scroll to parking section
                const parkingSection = document.querySelector('.parking-section');
                if (parkingSection) {
                    parkingSection.scrollIntoView({ behavior: 'smooth' });
                }
            } else if (this.textContent.includes('Learn More')) {
                // Navigate to about page
                window.location.href = '?page=about';
            }
        });
    });
    
    // CTA buttons
    const ctaButtons = document.querySelectorAll('.cta-buttons .btn');
    
    ctaButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.textContent.includes('Call Now')) {
                showNotification('Opening phone dialer...', 'info');
                // Here you would typically open the phone dialer
                window.location.href = 'tel:+15551234567';
            } else if (this.textContent.includes('Live Chat')) {
                showNotification('Live chat would open here', 'info');
                // Here you would typically open a chat widget
            }
        });
    });
}

// Form handling
function initializeForms() {
    const contactForm = document.querySelector('.contact-form');
    
    if (contactForm) {
        // Add real-time validation
        const inputs = contactForm.querySelectorAll('input, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                // Clear any existing error states when user starts typing
                clearFieldError(this);
            });
        });
        
        // Handle form submission with JavaScript enhancement
        contactForm.addEventListener('submit', function(e) {
            // Let the PHP handle the submission, but add loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            submitButton.textContent = 'Sending...';
            submitButton.disabled = true;
            
            // Re-enable after a delay (the page will reload anyway)
            setTimeout(() => {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            }, 2000);
        });
    }
}

// Field validation
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'This field is required';
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            message = 'Please enter a valid email address';
        }
    }
    
    if (!isValid) {
        showFieldError(field, message);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.style.borderColor = 'var(--destructive)';
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.style.color = 'var(--destructive)';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '0.25rem';
    errorElement.textContent = message;
    
    field.parentNode.appendChild(errorElement);
}

function clearFieldError(field) {
    field.style.borderColor = '';
    
    const errorElement = field.parentNode.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        max-width: 400px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    // Set colors based on type
    if (type === 'success') {
        notification.style.borderLeftColor = 'var(--success)';
        notification.style.borderLeftWidth = '4px';
    } else if (type === 'error') {
        notification.style.borderLeftColor = 'var(--destructive)';
        notification.style.borderLeftWidth = '4px';
    } else {
        notification.style.borderLeftColor = 'var(--primary)';
        notification.style.borderLeftWidth = '4px';
    }
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" style="
                background: none;
                border: none;
                font-size: 1.2rem;
                cursor: pointer;
                color: var(--muted-foreground);
                margin-left: auto;
            ">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Smooth scrolling for anchor links
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'A' && e.target.getAttribute('href')?.startsWith('#')) {
        e.preventDefault();
        const targetId = e.target.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

// Add loading states for page navigation
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('nav-link')) {
        const link = e.target;
        const originalText = link.textContent;
        
        // Add loading state
        link.style.opacity = '0.7';
        
        // Reset after page load (this will be interrupted by navigation)
        setTimeout(() => {
            link.style.opacity = '1';
        }, 500);
    }
});

// Add hover effects for cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.parking-card, .info-card, .stat-card, .value-card, .team-card, .faq-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});