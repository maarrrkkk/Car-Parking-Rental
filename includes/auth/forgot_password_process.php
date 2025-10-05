<?php
require_once '../../config/database.php';

// Load environment variables
$envFile = __DIR__ . '/../../.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : [];

$email = trim($_POST['email']);

if (empty($email)) {
    header("Location: ../../index.php?page=forgot_password&status=error&message=Email is required");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../index.php?page=forgot_password&status=error&message=Invalid email address");
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT id, firstname FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Don't reveal if email exists or not for security
    header("Location: ../../index.php?page=forgot_password&status=success&message=If an account with that email exists, we have sent a password reset link.");
    exit;
}

// Generate reset token
$resetToken = bin2hex(random_bytes(32));
$resetExpires = gmdate('Y-m-d H:i:s', strtotime('+1 hour'));

// Update user with reset token
$stmt = $pdo->prepare("UPDATE users SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id");
$stmt->execute([
    'token' => $resetToken,
    'expires' => $resetExpires,
    'id' => $user['id']
]);

// Send reset email
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $env['SMTP_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $env['SMTP_USERNAME'] ?? 'yourgmail@gmail.com';
    $mail->Password = $env['SMTP_PASSWORD'] ?? 'your_app_password';
    $mail->SMTPSecure = $env['SMTP_ENCRYPTION'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $env['SMTP_PORT'] ?? 587;

    $mail->setFrom($env['SMTP_USERNAME'] ?? 'yourgmail@gmail.com', 'Car Parking Rental');
    $mail->addReplyTo($env['SMTP_USERNAME'] ?? 'yourgmail@gmail.com', 'Car Parking Rental Support');
    $mail->addAddress($email, $user['firstname']);

    $resetLink = "http://localhost" . $baseUrl . "/index.php?page=reset_password&token=" . $resetToken;

    $mail->isHTML(false);
    $mail->Subject = 'Password Reset - Car Parking Rental';
    $mail->Body = "Hello " . $user['firstname'] . ",\n\nYou requested a password reset for your Car Parking Rental account.\n\nClick the link below to reset your password:\n\n" . $resetLink . "\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nCar Parking Rental Team";

    $mail->send();

    header("Location: ../../index.php?page=forgot_password&status=success&message=If an account with that email exists, we have sent a password reset link.");
    exit;
} catch (Exception $e) {
    header("Location: ../../index.php?page=forgot_password&status=error&message=Failed to send reset email. Please try again.");
    exit;
}
?>