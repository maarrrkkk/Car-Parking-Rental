<?php
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$prefillCode = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : '';
?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
        <h3 class="text-center mb-4">Verify Your Email</h3>
        <p class="text-center mb-4">We've sent a 6-digit verification code to <strong><?php echo $email; ?></strong></p>

        <?php if ($prefillCode): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Development mode: Verification code has been pre-filled for testing.
        </div>
        <?php endif; ?>

        <?php if ($status === 'error' && $message): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <form id="verifyForm" method="POST" action="./includes/auth/verify_email_process.php">
            <input type="hidden" name="email" value="<?php echo $email; ?>">

            <div class="mb-3">
                <label for="code" class="form-label">Verification Code</label>
                <input
                    type="text"
                    class="form-control"
                    id="code"
                    name="code"
                    value="<?php echo $prefillCode; ?>"
                    placeholder="Enter 6-digit code"
                    required
                    pattern="^[0-9]{6}$"
                    maxlength="6"
                    inputmode="numeric"
                    title="Enter the 6-digit code from your email">
            </div>

            <button type="submit" class="btn btn-primary w-100">Verify Email</button>
        </form>

        <p class="text-center mt-3">
            Didn't receive the code?
            <a href="./includes/auth/resend_verification.php?email=<?php echo urlencode($email); ?>">Resend Code</a>
        </p>

        <p class="text-center mt-2 mb-0">
            <a href="?page=register">Back to Registration</a>
        </p>
    </div>
</div>