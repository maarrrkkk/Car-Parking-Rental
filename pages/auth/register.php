<?php
// Get old inputs (if any) to repopulate the form after error
$old_firstname = isset($_GET['firstname']) ? htmlspecialchars($_GET['firstname']) : '';
$old_lastname = isset($_GET['lastname']) ? htmlspecialchars($_GET['lastname']) : '';
$old_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$old_phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : '';

// Get error fields (comma separated string, e.g., "email,phone")
$error_fields = isset($_GET['errors']) ? explode(',', $_GET['errors']) : [];
function isInvalid($field, $error_fields)
{
    return in_array($field, $error_fields) ? 'is-invalid' : '';
}
?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
        <h3 class="text-center mb-4">Create Account</h3>
        <form id="registerForm" method="POST" action="./includes/auth/register_process.php">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input
                        type="text"
                        class="form-control <?= isInvalid('firstname', $error_fields) ?>"
                        id="firstname"
                        name="firstname"
                        value="<?= $old_firstname ?>"
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input
                        type="text"
                        class="form-control <?= isInvalid('lastname', $error_fields) ?>"
                        id="lastname"
                        name="lastname"
                        value="<?= $old_lastname ?>"
                        required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    class="form-control <?= isInvalid('email', $error_fields) ?>"
                    id="email"
                    name="email"
                    value="<?= $old_email ?>"
                    required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input
                    type="text"
                    class="form-control <?= isInvalid('phone', $error_fields) ?>"
                    id="phone"
                    name="phone"
                    value="<?= $old_phone ?>"
                    required
                    pattern="^[0-9]{11}$"
                    maxlength="11"
                    inputmode="numeric"
                    title="Phone number must be exactly 11 digits and numbers only">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control <?= isInvalid('password', $error_fields) ?>"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required
                    pattern="^(?=.*[A-Z])[A-Za-z0-9]{11,}$"
                    title="Password must be at least 11 characters, include at least 1 uppercase letter, and contain only letters and numbers">
                <div class="invalid-feedback">
                    Password must be at least 11 characters, include 1 uppercase, and only letters/numbers.
                </div>
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    class="form-control <?= isInvalid('confirm_password', $error_fields) ?>"
                    id="confirmPassword"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                    pattern="^(?=.*[A-Z])[A-Za-z0-9]{11,}$"
                    title="Confirm password must follow the same rules as password">
                <div class="invalid-feedback">
                    Passwords must match and follow the password rules.
                </div>
            </div>


            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3 mb-0">Already have an account? <a href="?page=login">Login</a></p>
    </div>
</div>