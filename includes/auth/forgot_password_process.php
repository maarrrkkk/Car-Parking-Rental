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

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset - Car Parking Rental';

    // HTML email with styled button
    $mail->Body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f8f9fa; padding: 30px; border-radius: 0 0 5px 5px; }
            .button { display: inline-block; background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center; }
            .button:hover { background-color: #218838; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Password Reset Request</h2>
            </div>
            <div class='content'>
                <p>Hello " . htmlspecialchars($user['firstname']) . ",</p>

                <p>You requested a password reset for your Car Parking Rental account.</p>

                <p>Please click the button below to reset your password:</p>

                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . htmlspecialchars($resetLink) . "' class='button'>Reset My Password</a>
                </p>

                <p><strong>Important:</strong> This link will expire in 1 hour for security reasons.</p>

                <p>If you didn't request this password reset, please ignore this email. Your account will remain secure.</p>

                <p>Best regards,<br>Car Parking Rental Team</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>";

    // Plain text alternative for email clients that don't support HTML
    $mail->AltBody = "Hello " . $user['firstname'] . ",\n\nYou requested a password reset for your Car Parking Rental account.\n\nClick the link below to reset your password:\n\n" . $resetLink . "\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.\n\nBest regards,\nCar Parking Rental Team";

    $mail->send();

    header("Location: ../../index.php?page=forgot_password&status=success&message=If an account with that email exists, we have sent a password reset link.");
    exit;
} catch (Exception $e) {
    header("Location: ../../index.php?page=forgot_password&status=error&message=Failed to send reset email. Please try again.");
    exit;
}
?>