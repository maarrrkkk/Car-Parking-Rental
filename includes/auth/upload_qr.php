<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . $baseUrl . "/index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_image'])) {
    $file = $_FILES['qr_image'];


    // Validate file
    $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['qr_upload_error'] = 'Invalid file type. Only PNG, JPG, and JPEG are allowed.';
        header("Location: " . $baseUrl . "/pages/admin/index.php?current_page=profile");
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $_SESSION['qr_upload_error'] = 'File size too large. Maximum 5MB allowed.';
        header("Location: " . $baseUrl . "/pages/admin/index.php?current_page=profile");
        exit;
    }

    // Create directory if not exists
    $uploadDir = '../../assets/images/gcashQrcode/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Remove existing QR codes
    $existingFiles = glob($uploadDir . '*.png') + glob($uploadDir . '*.jpg') + glob($uploadDir . '*.jpeg');
    foreach ($existingFiles as $existingFile) {
        unlink($existingFile);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'gcash_qr_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $_SESSION['qr_upload_success'] = 'QR code uploaded successfully! File: ' . $filename;
    } else {
        $_SESSION['qr_upload_error'] = 'Failed to upload QR code. Check folder permissions.';
        error_log("Move failed: " . $filepath);
    }
} else {
    $_SESSION['qr_upload_error'] = 'No file uploaded.';
    header("Location: /Github/Car-Parking-Rental/pages/admin/index.php?current_page=profile");
    exit;
}

header("Location: ../../pages/admin/index.php?current_page=profile");
exit;
?>