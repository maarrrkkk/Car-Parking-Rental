<?php
require_once '../../config/database.php';

// Check if logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php?page=login");
    exit;
}

// Fetch current admin data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "No admin found.";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Optional: Password update
    $passwordQuery = "";
    $params = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'phone' => $phone,
        'email' => $email,
        'id' => $_SESSION['user_id']
    ];

    if (!empty($_POST['password'])) {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $passwordQuery = ", password = :password";
        $params['password'] = $hashedPassword;
    }

    $update = $pdo->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, phone = :phone, email = :email $passwordQuery WHERE id = :id");
    $update->execute($params);

    header("Location: profile.php?updated=1");
    exit;
}
?>
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-warning text-dark text-center">
            <h3>Edit Profile</h3>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($admin['firstname']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($admin['lastname']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($admin['phone']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']); ?>" required>
                </div>
                <!-- ✅ New Password Fields -->
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Enter new password"
                        pattern="^(?=.*[A-Z])[A-Za-z0-9]{11,}$"
                        title="Password must be at least 11 characters, include at least 1 uppercase letter, and contain only letters and numbers">
                    <div class="form-text">Leave blank if you don’t want to change it.</div>
                    <div class="invalid-feedback">
                        Password must be at least 11 characters, include 1 uppercase, and only letters/numbers.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                    <input
                        type="password"
                        class="form-control"
                        id="confirmPassword"
                        name="confirm_password"
                        placeholder="Confirm new password"
                        pattern="^(?=.*[A-Z])[A-Za-z0-9]{11,}$"
                        title="Confirm password must follow the same rules as password">
                    <div class="invalid-feedback">
                        Passwords must match and follow the password rules.
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">💾 Save Changes</button>
                <a href="?current_page=profile" <?= $current_page === 'profile' ? 'active' : '' ?> class="btn btn-secondary w-100 mt-2">⬅ Back</a>
            </form>
        </div>
    </div>
</div>