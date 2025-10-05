<?php
require_once '../../config/database.php';

$email = trim($_POST['email']);
$code = trim($_POST['code']);

if (empty($email) || empty($code)) {
    header("Location: ../../index.php?page=verify_email&email=" . urlencode($email) . "&status=error&message=All fields are required");
    exit;
}

// Check if user exists and code matches
$stmt = $pdo->prepare("
    SELECT id, email_verification_code, verification_expires
    FROM users
    WHERE email = :email AND email_verified = 0
");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../../index.php?page=verify_email&email=" . urlencode($email) . "&status=error&message=Invalid verification request");
    exit;
}

if ($user['email_verification_code'] != $code) {
    header("Location: ../../index.php?page=verify_email&email=" . urlencode($email) . "&status=error&message=Invalid verification code");
    exit;
}

if (strtotime($user['verification_expires']) < time()) {
    header("Location: ../../index.php?page=verify_email&email=" . urlencode($email) . "&status=error&message=Verification code has expired");
    exit;
}

// Update user as verified
$stmt = $pdo->prepare("
    UPDATE users
    SET email_verified = 1, status = 'Active', email_verification_code = NULL, verification_expires = NULL
    WHERE id = :id
");
$stmt->execute(['id' => $user['id']]);

header("Location: ../../index.php?page=login&status=success&message=Email verified successfully. You can now login.");
exit;
?>