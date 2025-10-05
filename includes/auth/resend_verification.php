<?php
require_once '../../config/database.php';

// Load environment variables
$envFile = __DIR__ . '/../../.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : [];

$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if (empty($email)) {
    header("Location: ../../index.php?page=register&status=error&message=Invalid request");
    exit;
}

// Check if user exists and not verified
$stmt = $pdo->prepare("SELECT id, firstname, lastname FROM users WHERE email = :email AND email_verified = 0");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../index.php?page=register&status=error&message=User not found or already verified");
    exit;
}

// Generate new code
$verificationCode = rand(100000, 999999);
$verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Update code
$stmt = $pdo->prepare("UPDATE users SET email_verification_code = :code, verification_expires = :expires WHERE id = :id");
$stmt->execute([
    'code' => $verificationCode,
    'expires' => $verificationExpires,
    'id' => $user['id']
]);

// Send email
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
    $mail->addAddress($email, $user['firstname'] . ' ' . $user['lastname']);

    $mail->isHTML(false);
    $mail->Subject = 'New Verification Code - Car Parking Rental';
    $mail->Body = "Hello " . $user['firstname'] . ",\n\nYour new verification code is: $verificationCode\n\nThis code will expire in 24 hours.\n\nPlease enter this code on the verification page to activate your account.\n\nBest regards,\nCar Parking Rental Team";

    $mail->send();

    header("Location: ../../index.php?page=verify_email&email=" . urlencode($email) . "&status=success&message=New verification code sent");
    exit;
} catch (Exception $e) {
    header("Location: ../../index.php?page=verify_email&email=" . urlencode($email) . "&status=error&message=Failed to send verification email");
    exit;
}
?>