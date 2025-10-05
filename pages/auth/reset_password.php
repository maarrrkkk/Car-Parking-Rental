<?php
require_once __DIR__ . '/../../config/database.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

if (empty($token)) {
    header("Location: " . $baseUrl . "/index.php?page=login");
    exit;
}

// Get email from token for accessibility
$email = '';
try {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE password_reset_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $email = $user['email'];
    }
} catch (Exception $e) {
    // Ignore
}
?>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 450px;">
        <h3 class="text-center mb-4">Reset Password</h3>
        <p class="text-center mb-4">Enter your new password below.</p>

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

        <form id="resetForm" method="POST" action="includes/auth/reset_password_process.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($email); ?>">

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter new password"
                    autocomplete="new-password"
                    required
                    pattern="^(?=.*[A-Z])[A-Za-z0-9]{11,}$"
                    title="Password must be at least 11 characters, include at least 1 uppercase letter, and contain only letters and numbers">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm new password"
                    autocomplete="new-password"
                    required
                    pattern="^(?=.*[A-Z])[A-Za-z0-9]{11,}$"
                    title="Confirm password must match the new password">
            </div>

            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>

        <p class="text-center mt-3 mb-0">
            <a href="<?php echo $baseUrl; ?>/index.php?page=login">Back to Login</a>
        </p>
    </div>
</div>