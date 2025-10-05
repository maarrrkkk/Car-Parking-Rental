// =============================
// Main JavaScript functionality for Car Parking Rental
// =============================

document.addEventListener('DOMContentLoaded', function () {
    // Initialize base functionality
    initializeNavigation();
    initializeButtons();
    initializeForms();
    initializeCards();

    // Registration & Login Scripts
    handleRegistrationStatus();
    handleFieldHighlighting();
    handlePasswordValidation();
    handlePhoneValidation();
});

document.getElementById("check").addEventListener("change", function() {
    let nav = document.getElementById("navbarNav");
    let bsCollapse = new bootstrap.Collapse(nav, { toggle: false });
    if (this.checked) {
        bsCollapse.show();
    } else {
        bsCollapse.hide();
    }
});

// =============================
// Booking & Details Handlers
// =============================
function handleBooking(spaceId, isLoggedIn) {
    if (isLoggedIn) {
        // User logged in → redirect to booking page
        window.location.href = "index.php?page=booking&id=" + spaceId;
    } else {
        // User not logged in → redirect to login page
        window.location.href = "index.php?page=login&status=error&message=Please login first.";
    }
}

function handleViewDetails(spaceId, isLoggedIn) {
    if (isLoggedIn) {
        // User logged in → redirect to details page
        window.location.href = "index.php?page=details&id=" + spaceId;
    } else {
        // User not logged in → redirect to login page
        window.location.href = "index.php?page=login&status=error&message=Please login first.";
    }
}

// =============================
// Navigation functionality
// =============================
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            // Placeholder for future navigation logic
        });
    });
}

// =============================
// Registration Success Popup
// =============================
function handleRegistrationStatus() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get("status");
    const message = urlParams.get("message");

    if (status === "success" && typeof Swal !== "undefined") {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            showConfirmButton: false,
            timer: 2500
        }).then(() => {
            window.location.href = '?page=login';
        });
    }
}

// =============================
// Highlight invalid fields
// =============================
function handleFieldHighlighting() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get("status");
    const field = urlParams.get("field");

    if (status === "error") {
        if (field === "email") {
            document.getElementById("email")?.classList.add("is-invalid");
        }
        if (field === "name") {
            document.getElementById("firstName")?.classList.add("is-invalid");
            document.getElementById("lastName")?.classList.add("is-invalid");
        }
        if (field === "password") {
            document.getElementById("password")?.classList.add("is-invalid");
            document.getElementById("confirmPassword")?.classList.add("is-invalid");
        }
        if (field === "phone") {
            document.getElementById("phone")?.classList.add("is-invalid");
        }
        if (field === "all") {
            document.querySelectorAll("input").forEach(input => input.classList.add("is-invalid"));
        }
    }
}

// =============================
// Password & Confirm Validation
// =============================

function handlePasswordValidation() {
    // Just grab the first form on this page instead of checking action
    const form = document.querySelector('#registerForm');
    if (!form) return;

    const pwd = document.getElementById('password');
    const cpwd = document.getElementById('confirmPassword');
    const pwdRegex = /^(?=.*[A-Z])[A-Za-z0-9]{11,}$/;

    function validatePasswordField() {
        const ok = pwdRegex.test(pwd.value);
        pwd.classList.toggle('is-invalid', !ok);
        return ok;
    }

    function validateConfirmField() {
        const match = pwd.value !== "" && pwd.value === cpwd.value;
        cpwd.classList.toggle('is-invalid', !match);
        return match;
    }

    pwd?.addEventListener('input', () => {
        validatePasswordField();
        if (cpwd.value.length > 0) validateConfirmField();
    });

    cpwd?.addEventListener('input', () => validateConfirmField());

    form.addEventListener('submit', function (e) {
        const okPwd = validatePasswordField();
        const okMatch = validateConfirmField();
        if (!okPwd || !okMatch) {
            e.preventDefault();
            form.querySelector('.is-invalid')?.focus();
        }
    });
}


// =============================
// Phone Number Validation
// =============================
function handlePhoneValidation() {
    const phoneInput = document.getElementById("phone");
    if (!phoneInput) return;

    phoneInput.addEventListener("input", function () {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, "");
        // Enforce 11 characters max
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });
}

// =============================
// Button Interactions
// =============================
function initializeButtons() {
    // Booking buttons
    const bookingButtons = document.querySelectorAll('.parking-card .btn');
    bookingButtons.forEach(button => {
        if (!button.disabled) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const cardTitle = this.closest('.parking-card').querySelector('.card-title').textContent;
                if (this.textContent.trim() === 'Book Now') {
                    showNotification(`Booking initiated for ${cardTitle}`, 'success');
                } else if (this.textContent.trim() === 'Waitlist') {
                    showNotification(`Added to waitlist for ${cardTitle}`, 'info');
                }
            });
        }
    });

    // Hero buttons
    const heroButtons = document.querySelectorAll('.hero-buttons .btn');
    heroButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            if (this.textContent.includes('Find Parking')) {
                document.querySelector('.parking-section')?.scrollIntoView({ behavior: 'smooth' });
            } else if (this.textContent.includes('Learn More')) {
                window.location.href = '?page=about';
            }
        });
    });

    // CTA buttons
    const ctaButtons = document.querySelectorAll('.cta-buttons .btn');
    ctaButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            if (this.textContent.includes('Call Now')) {
                showNotification('Opening phone dialer...', 'info');
                window.location.href = 'tel:+15551234567';
            } else if (this.textContent.includes('Live Chat')) {
                showNotification('Live chat would open here', 'info');
            }
        });
    });
}

// =============================
// Forms (Contact Page)
// =============================
function initializeForms() {
    const contactForm = document.querySelector('.contact-form');
    if (!contactForm) return;

    const inputs = contactForm.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => clearFieldError(input));
    });

    contactForm.addEventListener('submit', function () {
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Sending...';
        submitButton.disabled = true;
        setTimeout(() => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }, 2000);
    });
}

// =============================
// Cards Hover Effect
// =============================
function initializeCards() {
    const cards = document.querySelectorAll('.parking-card, .info-card, .stat-card, .value-card, .team-card, .faq-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
        });
        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });
}

// =============================
// Field Validation Helpers
// =============================
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';

    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = 'This field is required';
    }

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
    if (errorElement) errorElement.remove();
}

// =============================
// Notification System
// =============================
function showNotification(message, type = 'info') {
    document.querySelectorAll('.notification').forEach(n => n.remove());
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
    setTimeout(() => { notification.style.transform = 'translateX(0)'; }, 100);
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}
