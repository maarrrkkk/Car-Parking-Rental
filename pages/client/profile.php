<?php
require_once __DIR__ . '/../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php?page=login&status=error&message=Please login first.");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch user's bookings
$stmt = $pdo->prepare("
    SELECT b.*, s.name as slot_name
    FROM bookings b
    JOIN slots s ON b.slot_id = s.id
    WHERE b.user_id = :user_id
    ORDER BY b.created_at DESC
");
$stmt->execute(['user_id' => $userId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <div class="container mt-5">
        <h1 class="mb-4">My Profile</h1>

        <div class="row">
            <!-- Credentials Section -->
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h5>My Credentials</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-<?= $user['status'] === 'Active' ? 'success' : 'warning' ?>"><?= htmlspecialchars($user['status']) ?></span></p>
                        <p><strong>Member Since:</strong> <?= date('M d, Y', strtotime($user['member_since'])) ?></p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCredentialsModal">Edit Credentials</button>
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="card shadow">
                    <div class="card-header">
                        <h5>Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Booked Slots Section -->
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h5>My Booked Slots</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($bookings)): ?>
                            <p>No bookings found.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($bookings as $booking): ?>
                                    <div class="list-group-item">
                                        <h6 class="mb-1"><?= htmlspecialchars($booking['slot_name']) ?></h6>
                                        <p class="mb-1">Start: <?= date('M d, Y H:i', strtotime($booking['start_time'])) ?></p>
                                        <p class="mb-1">End: <?= $booking['end_time'] ? date('M d, Y H:i', strtotime($booking['end_time'])) : 'Ongoing' ?></p>
                                        <p class="mb-1">Status: <span class="badge bg-<?= $booking['status'] === 'pending' ? 'info' : ($booking['status'] === 'active' ? 'warning' : ($booking['status'] === 'completed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'secondary'))) ?>"><?= ucfirst($booking['status']) ?></span></p>
                                        <p class="mb-0">Amount: ₱<?= number_format($booking['amount'], 2) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Credentials Modal -->
    <div class="modal fade" id="editCredentialsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Credentials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCredentialsForm">
                        <div class="mb-3">
                            <label for="editFirstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editFirstName" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editLastName" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editPhone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Change Password Form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const current = document.getElementById('currentPassword').value;
            const newPass = document.getElementById('newPassword').value;
            const confirm = document.getElementById('confirmPassword').value;

            if (newPass !== confirm) {
                alert('New passwords do not match.');
                return;
            }

            fetch('<?php echo $baseUrl; ?>/api/user.php?action=change_password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ current_password: current, new_password: newPass })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully!');
                    this.reset();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error(error);
            });
        });

        // Edit Credentials Form
        document.getElementById('editCredentialsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const firstName = document.getElementById('editFirstName').value;
            const lastName = document.getElementById('editLastName').value;
            const email = document.getElementById('editEmail').value;
            const phone = document.getElementById('editPhone').value;

            fetch('<?php echo $baseUrl; ?>/api/user.php?action=update_profile', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ firstname: firstName, lastname: lastName, email: email, phone: phone })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
                console.error(error);
            });
        });
    </script>
</body>
</html>