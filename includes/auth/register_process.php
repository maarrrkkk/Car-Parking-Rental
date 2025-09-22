<?php
require_once '../../config/database.php';

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

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user with default values
$stmt = $pdo->prepare("
    INSERT INTO users 
    (firstname, lastname, email, phone, password, status, total_bookings, total_spent, member_since, last_login) 
    VALUES 
    (:firstname, :lastname, :email, :phone, :password, 'Active', 0, 0.00, NOW(), NULL)
");
$stmt->execute([
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'phone' => $phone,
    'password' => $hashedPassword
]);

header("Location: ../../index.php?page=register&status=success&message=Account created successfully");
exit;
?>
