<?php
header('Content-Type: application/json');

// Load environment variables
$envFile = __DIR__ . '/../.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings for Gmail SMTP
    $mail->isSMTP();
    $mail->Host = $env['SMTP_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $env['SMTP_USERNAME'] ?? 'yourgmail@gmail.com';
    $mail->Password = $env['SMTP_PASSWORD'] ?? 'your_app_password';
    $mail->SMTPSecure = $env['SMTP_ENCRYPTION'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $env['SMTP_PORT'] ?? 587;

    // Recipients
    $mail->setFrom($env['SMTP_USERNAME'] ?? 'yourgmail@gmail.com', 'Car Parking Rental');
    $mail->addReplyTo($email, $name);
    $mail->addAddress($env['SMTP_USERNAME'] ?? 'yourgmail@gmail.com', 'Car Parking Rental Support');

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Contact Message from ' . $name . ' - ' . $subject;
    $mail->Body = "Name: $name\nEmail: $email\nSubject: $subject\n\nMessage:\n$message";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $mail->ErrorInfo]);
}
?>