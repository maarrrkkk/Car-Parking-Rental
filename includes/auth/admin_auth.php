<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config to get base URL
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . $baseUrl . '/index.php?page=login');
    exit();
}