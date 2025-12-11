<?php
require_once '../../config/database.php';

// Load environment variables
$envFile = __DIR__ . '/../../.env';
$env = file_exists($envFile) ? parse_ini_file($envFile) : [];

$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

$errors = [];

// Check if fullname exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE firstname = :firstname AND lastname = :lastname");
$stmt->execute(['firstname' => $firstname, 'lastname' => $lastname]);
if ($stmt->rowCount() > 0) {
    $errors[] = 'firstname';
    $errors[] = 'lastname';
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->rowCount() > 0) {
    $errors[] = 'email';
}

// Check if phone exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE phone = :phone");
$stmt->execute(['phone' => $phone]);
if ($stmt->rowCount() > 0) {
    $errors[] = 'phone';
}

// Check password confirmation
if ($password !== $confirm_password) {
    $errors[] = 'password';
    $errors[] = 'confirm_password';
}

// If there are any errors
if (!empty($errors)) {
    $error_fields = implode(',', array_unique($errors));
    header("Location: ../../index.php?page=register&status=error&message=Please fix the highlighted fields&firstname=$firstname&lastname=$lastname&email=$email&phone=$phone&errors=$error_fields");
    exit;
}

// Generate verification code
$verificationCode = rand(100000, 999999);
$verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user with verification details
$stmt = $pdo->prepare("
    INSERT INTO users
    (firstname, lastname, email, phone, password, status, email_verification_code, verification_expires, total_bookings, total_spent, member_since, last_login)
    VALUES
    (:firstname, :lastname, :email, :phone, :password, 'Inactive', :code, :expires, 0, 0.00, NOW(), NULL)
");
$stmt->execute([
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'phone' => $phone,
    'password' => $hashedPassword,
    'code' => $verificationCode,
    'expires' => $verificationExpires
]);

$userId = $pdo->lastInsertId();

// Send verification email using PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = $env['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $env['SMTP_USERNAME'];
    $mail->Password = $env['SMTP_PASSWORD'];
    $mail->SMTPSecure = $env['SMTP_ENCRYPTION'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $env['SMTP_PORT'];

    // Recipients
    $mail->setFrom($env['SMTP_USERNAME'], 'Car Parking Rental');
    $mail->addReplyTo($env['SMTP_USERNAME'], 'Car Parking Rental Support');
    $mail->addAddress($email, $firstname . ' ' . $lastname);

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'Verify Your Email - Car Parking Rental';
    $mail->Body = "Hello $firstname,\n\nThank you for registering with Car Parking Rental.\n\nYour verification code is: $verificationCode\n\nThis code will expire in 24 hours.\n\nPlease enter this code on the verification page to activate your account.\n\nBest regards,\nCar Parking Rental Team";

    $mail->send();
} catch (Exception $e) {
    // Log error but continue with registration
    error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
}

// Redirect to verification page
header("Location: ../../index.php?page=verify_email&email=" . urlencode($email));
exit;
?>
