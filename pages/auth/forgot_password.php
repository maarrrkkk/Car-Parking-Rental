<?php
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
        <h3 class="text-center mb-4">Forgot Password</h3>
        <p class="text-center mb-4">Enter your email address and we'll send you a link to reset your password.</p>

        <?php if ($status === 'error' && $message): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <?php if ($status === 'success' && $message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <form id="forgotForm" method="POST" action="./includes/auth/forgot_password_process.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>

        <p class="text-center mt-3 mb-0">
            <a href="?page=login">Back to Login</a>
        </p>
    </div>
</div>