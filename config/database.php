<?php
/**
 * Car Parking Rental System - Database Connection
 * Auto-run setup if DB/tables are missing
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0) continue; // Skip comments
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
}

// Base URL configuration - change this to match your installation path
$baseUrl = $env['BASE_URL'] ?? '/Car-Parking-Rental';

// Database configuration with fallback defaults
$host = $env['DB_HOST'] ?? "localhost";
$dbname = $env['DB_NAME'] ?? "car_parking_rental_db";
$username = $env['DB_USER'] ?? "root";
$password = $env['DB_PASS'] ?? "";
$port = $env['DB_PORT'] ?? "3306";

// PayPal configuration
$paypalClientId = $env['PAYPAL_CLIENT_ID'] ?? "";
$paypalClientSecret = $env['PAYPAL_CLIENT_SECRET'] ?? "";
$paypalEnvironment = $env['PAYPAL_ENVIRONMENT'] ?? "sandbox";

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

        // Create waitlist table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS waitlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            slot_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (slot_id) REFERENCES slots(id),
            UNIQUE KEY unique_user_slot (user_id, slot_id)
        )");

    }

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        // Run setup if DB missing
        require_once __DIR__ . '/../setup.php';
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}
