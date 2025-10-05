<?php
/**
 * Car Parking Rental System - Database Connection
 * Auto-run setup if DB/tables are missing
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
} else {
    $env = [];
}

// Base URL configuration - change this to match your installation path
$baseUrl = $env['BASE_URL'] ?? '/Car-Parking-Rental';

// Database configuration with fallback defaults
$host = $env['DB_HOST'] ?? "localhost";
$dbname = $env['DB_NAME'] ?? "car_parking_rental_db";
$username = $env['DB_USER'] ?? "root";
$password = $env['DB_PASS'] ?? "";
$port = $env['DB_PORT'] ?? "3306";

try {
    // Try to connect directly to the DB
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if a critical table exists (users)
    $check = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($check->rowCount() === 0) {
        // Run setup if schema missing
        require_once __DIR__ . '/../setup.php';
    } else {
        // Ensure table has all required columns (for existing databases)
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_code VARCHAR(255) NULL");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_expires TIMESTAMP NULL");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) NULL");
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_expires TIMESTAMP NULL");

        // Check if slots table exists before altering
        $checkSlots = $pdo->query("SHOW TABLES LIKE 'slots'");
        if ($checkSlots->rowCount() > 0) {
            $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS motorcycle_rate DECIMAL(10,2) DEFAULT 50.00");
            $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS car_rate DECIMAL(10,2) DEFAULT 150.00");
            $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS suv_rate DECIMAL(10,2) DEFAULT 200.00");
            $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS van_rate DECIMAL(10,2) DEFAULT 250.00");
            $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS truck_rate DECIMAL(10,2) DEFAULT 300.00");
            $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS mini_truck_rate DECIMAL(10,2) DEFAULT 350.00");
        }

        // Check if bookings table exists before altering
        $checkBookings = $pdo->query("SHOW TABLES LIKE 'bookings'");
        if ($checkBookings->rowCount() > 0) {
            $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS vehicle_type ENUM('motorcycle','car','suv','van','truck','mini_truck') NOT NULL DEFAULT 'car'");
            $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS amount DECIMAL(10,2) NOT NULL DEFAULT 0.00");
            $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS receipt VARCHAR(255) NULL");
            $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS paid_at TIMESTAMP NULL");
        }

    }

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        // Run setup if DB missing
        require_once __DIR__ . '/../setup.php';
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}
