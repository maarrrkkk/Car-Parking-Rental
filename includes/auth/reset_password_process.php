<?php
require_once '../../config/database.php';

$token = trim($_POST['token']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (empty($token) || empty($password) || empty($confirm_password)) {
    header("Location: ../../index.php?page=reset_password&token=" . urlencode($token) . "&status=error&message=All fields are required");
    exit;
}

if ($password !== $confirm_password) {
    header("Location: ../../index.php?page=reset_password&token=" . urlencode($token) . "&status=error&message=Passwords do not match");
    exit;
}

// Validate password strength (same as registration)
if (!preg_match('/^(?=.*[A-Z])[A-Za-z0-9]{11,}$/', $password)) {
    header("Location: ../../index.php?page=reset_password&token=" . urlencode($token) . "&status=error&message=Password must be at least 11 characters, include at least 1 uppercase letter, and contain only letters and numbers");
    exit;
}

// Check if token is valid
$stmt = $pdo->prepare("SELECT id FROM users WHERE password_reset_token = :token AND password_reset_expires > UTC_TIMESTAMP()");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../index.php?page=reset_password&token=" . urlencode($token) . "&status=error&message=Invalid or expired reset token");
    exit;
}

// Hash new password and update user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = :password, password_reset_token = NULL, password_reset_expires = NULL WHERE id = :id");
$stmt->execute([
    'password' => $hashedPassword,
    'id' => $user['id']
]);

header("Location: ../../index.php?page=login&status=success&message=Password reset successfully. You can now login with your new password.");
exit;
?>