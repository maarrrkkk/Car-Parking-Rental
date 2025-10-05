<?php
require_once '../../config/database.php';

// Check if logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?page=login");
    exit;
}

// Fetch the details of the currently logged-in admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "No admin found.";
    exit;
}
?>

<div class="py-5 ">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header text-dark text-start p-4">
            <h3>Admin Profile</h3>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <strong>Name:</strong> <?= htmlspecialchars($admin['firstname'] . " " . $admin['lastname']); ?>
            </div>
            <div class="mb-3">
                <strong>Email:</strong> <?= htmlspecialchars($admin['email']); ?>
            </div>
            <div class="mb-3">
                <strong>Phone:</strong> <?= htmlspecialchars($admin['phone']); ?>
            </div>
            <div class="mb-3">
                <strong>Status:</strong> <span class="badge bg-success"><?= htmlspecialchars($admin['status']); ?></span>
            </div>
            <div class="mb-3">
                <strong>Last Login:</strong> <?= htmlspecialchars($admin['last_login']); ?>
            </div>
            <a href="?current_page=edit_profile" class="btn btn-warning w-100 mt-3" <?= $current_page === 'edit_profile' ? 'active' : '' ?>>✏️ Edit Profile</a>
        </div>
    </div>

    <!-- QR Code Management -->
    <div class="card shadow-lg border-0 rounded-3 mt-4">
        <div class="card-header text-dark text-start p-4">
            <h3>QRCODE</h3>
        </div>
        <div class="card-body p-4">
            <?php
            // Display messages
            if (isset($_SESSION['qr_upload_success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['qr_upload_success']) . '</div>';
                unset($_SESSION['qr_upload_success']);
            }
            if (isset($_SESSION['qr_upload_error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['qr_upload_error']) . '</div>';
                unset($_SESSION['qr_upload_error']);
            }

            // Check if QR code exists
            $qrPath = '../../assets/images/gcashQrcode/';
            $qrFiles = glob($qrPath . '*.png') ?: glob($qrPath . '*.jpg') ?: glob($qrPath . '*.jpeg') ?: [];
            $currentQr = !empty($qrFiles) ? basename($qrFiles[0]) : null;
            ?>

            <?php if ($currentQr): ?>
                <div class="mb-3">
                    <strong>Current QR Code:</strong><br>
                    <img src="<?= htmlspecialchars($qrPath . $currentQr) ?>" alt="GCash QR Code" class="img-fluid mt-2" style="max-width: 200px;">
                    <p class="mt-2"><small>File: <?= htmlspecialchars($currentQr) ?></small></p>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <p class="text-muted">No QR code uploaded yet.</p>
                </div>
            <?php endif; ?>

            <form action="/Github/Car-Parking-Rental/includes/auth/upload_qr.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="qr_image" class="form-label">Upload New QR Code</label>
                    <input type="file" class="form-control" id="qr_image" name="qr_image" accept="image/png,image/jpg,image/jpeg" required>
                    <div class="form-text">Accepted formats: PNG, JPG, JPEG</div>
                </div>
                <button type="submit" class="btn btn-primary">Upload QR Code</button>
            </form>
        </div>
    </div>
</div>