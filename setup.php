<?php
// Database setup script - Auto-run when DB/tables are missing

// Only show output if accessed directly
$showOutput = (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME']));

// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
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

// Database configuration with fallback defaults
$host = $env['DB_HOST'] ?? "localhost";
$dbname = $env['DB_NAME'] ?? "car_parking_rental_db";
$username = $env['DB_USER'] ?? "root";
$password = $env['DB_PASS'] ?? "";

try {
    // Connect to MySQL server only (no DB yet)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if ($showOutput) echo "Database '$dbname' created or already exists.<br>";

    // Reconnect with DB selected
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role ENUM('admin','user') DEFAULT 'user',
            firstname VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            status ENUM('Active','Inactive','Suspended') DEFAULT 'Inactive',
            email_verification_code VARCHAR(255) NULL,
            email_verified TINYINT(1) DEFAULT 0,
            verification_expires TIMESTAMP NULL,
            password_reset_token VARCHAR(255) NULL,
            password_reset_expires TIMESTAMP NULL,
            total_bookings INT DEFAULT 0,
            total_spent DECIMAL(10,2) DEFAULT 0.00,
            member_since TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    if ($showOutput) echo "Users table created.<br>";

    // Create slots table with correct fields
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS slots (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE, -- Slot A, Slot B, etc.
            hourly_rate DECIMAL(10,2) DEFAULT NULL,  -- base hourly rate
            daily_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00, -- base daily rate
            monthly_rate DECIMAL(10,2) DEFAULT NULL, -- base monthly rate
            motorcycle_rate DECIMAL(10,2) DEFAULT 50.00, -- additional for motorcycle
            car_rate DECIMAL(10,2) DEFAULT 150.00, -- additional for car
            suv_rate DECIMAL(10,2) DEFAULT 200.00, -- additional for SUV
            van_rate DECIMAL(10,2) DEFAULT 250.00, -- additional for van
            truck_rate DECIMAL(10,2) DEFAULT 300.00, -- additional for truck
            mini_truck_rate DECIMAL(10,2) DEFAULT 350.00, -- additional for mini truck
            available TINYINT(1) DEFAULT 1, -- 1 = available, 0 = not available
            image VARCHAR(255) DEFAULT NULL, -- image path
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    if ($showOutput) echo "Slots table created.<br>";

    // Add missing columns if they don't exist (for existing databases)
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_code VARCHAR(255) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS verification_expires TIMESTAMP NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_expires TIMESTAMP NULL");
    $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS motorcycle_rate DECIMAL(10,2) DEFAULT 50.00");
    $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS car_rate DECIMAL(10,2) DEFAULT 150.00");
    $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS suv_rate DECIMAL(10,2) DEFAULT 200.00");
    $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS van_rate DECIMAL(10,2) DEFAULT 250.00");
    $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS truck_rate DECIMAL(10,2) DEFAULT 300.00");
    $pdo->exec("ALTER TABLE slots ADD COLUMN IF NOT EXISTS mini_truck_rate DECIMAL(10,2) DEFAULT 350.00");


    // Create bookings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            slot_id INT NOT NULL,
            vehicle_type ENUM('motorcycle','car','suv','van','truck','mini_truck') NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME DEFAULT NULL,
            status ENUM('pending','active','completed','cancelled') DEFAULT 'pending',
            amount DECIMAL(10,2) NOT NULL,
            receipt VARCHAR(255) NULL,
            paid_at TIMESTAMP NULL,
            payment_method ENUM('paypal','gcash') DEFAULT 'paypal',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_slot_id (slot_id),
            INDEX idx_status (status),
            INDEX idx_start_time (start_time)
        ) ENGINE=InnoDB;
    ");
    if ($showOutput) echo "Bookings table created.<br>";

    // Create waitlist table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS waitlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            slot_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_slot (user_id, slot_id),
            INDEX idx_user_id (user_id),
            INDEX idx_slot_id (slot_id)
        ) ENGINE=InnoDB;
    ");
    if ($showOutput) echo "Waitlist table created.<br>";

    // Ensure bookings table has required columns (for existing databases)
    $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS vehicle_type ENUM('motorcycle','car','suv','van','truck','mini_truck') NOT NULL DEFAULT 'car'");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS amount DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS receipt VARCHAR(255) NULL");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS paid_at TIMESTAMP NULL");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS payment_method ENUM('paypal','gcash') DEFAULT 'paypal'");

    // Update status ENUM to include 'pending'
    $pdo->exec("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','active','completed','cancelled') DEFAULT 'pending'");

    // Insert default admin user if not exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $check->execute();
    $count = $check->fetchColumn();

    $adminFirstname = $env['ADMIN_FIRSTNAME'] ?? 'Super';
    $adminLastname = $env['ADMIN_LASTNAME'] ?? 'Admin';
    $adminEmail = $env['ADMIN_EMAIL'] ?? 'admin@carparking.com';
    $adminPhone = $env['ADMIN_PHONE'] ?? '09678451234';
    $adminPassword = $env['ADMIN_PASSWORD'] ?? 'Qwerty12345';

    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (role, firstname, lastname, email, phone, password, status, email_verified)
                                VALUES (:role, :firstname, :lastname, :email, :phone, :password, :status, 1)");
        $stmt->execute([
            ':role' => 'admin',
            ':firstname' => $adminFirstname,
            ':lastname'  => $adminLastname,
            ':email'     => $adminEmail,
            ':phone'     => $adminPhone,
            ':password'  => password_hash($adminPassword, PASSWORD_BCRYPT),
            ':status'    => 'Active'
        ]);
        if ($showOutput) echo "Default admin user created.<br>";
    } else {
        if ($showOutput) echo "Admin user already exists.<br>";
    }

    // Insert sample parking slots if none exist
    $checkSlots = $pdo->prepare("SELECT COUNT(*) FROM slots");
    $checkSlots->execute();
    $slotCount = $checkSlots->fetchColumn();

    if ($slotCount == 0) {
        $sampleSlots = [
            ['name' => 'Slot A1', 'hourly_rate' => 50.00, 'daily_rate' => 300.00, 'monthly_rate' => 5000.00],
            ['name' => 'Slot A2', 'hourly_rate' => 50.00, 'daily_rate' => 300.00, 'monthly_rate' => 5000.00],
            ['name' => 'Slot A3', 'hourly_rate' => 50.00, 'daily_rate' => 300.00, 'monthly_rate' => 5000.00],
            ['name' => 'Slot B1', 'hourly_rate' => 60.00, 'daily_rate' => 350.00, 'monthly_rate' => 6000.00],
            ['name' => 'Slot B2', 'hourly_rate' => 60.00, 'daily_rate' => 350.00, 'monthly_rate' => 6000.00],
            ['name' => 'Slot B3', 'hourly_rate' => 60.00, 'daily_rate' => 350.00, 'monthly_rate' => 6000.00],
            ['name' => 'Slot C1', 'hourly_rate' => 70.00, 'daily_rate' => 400.00, 'monthly_rate' => 7000.00],
            ['name' => 'Slot C2', 'hourly_rate' => 70.00, 'daily_rate' => 400.00, 'monthly_rate' => 7000.00],
            ['name' => 'Slot C3', 'hourly_rate' => 70.00, 'daily_rate' => 400.00, 'monthly_rate' => 7000.00],
            ['name' => 'Slot VIP1', 'hourly_rate' => 100.00, 'daily_rate' => 600.00, 'monthly_rate' => 12000.00]
        ];

        $stmt = $pdo->prepare("INSERT INTO slots (name, hourly_rate, daily_rate, monthly_rate, available) VALUES (:name, :hourly_rate, :daily_rate, :monthly_rate, 1)");
        
        foreach ($sampleSlots as $slot) {
            $stmt->execute($slot);
        }
        
        if ($showOutput) echo "Sample parking slots created.<br>";
    } else {
        if ($showOutput) echo "Parking slots already exist.<br>";
    }

    if ($showOutput) {
        echo "<br><strong>Database setup completed successfully!</strong><br>";
        echo "You can now access the application at index.php<br>";
        echo "<strong>Admin Login Credentials:</strong><br>";
        echo "Email: " . htmlspecialchars($adminEmail) . "<br>";
        echo "Password: " . htmlspecialchars($adminPassword) . "<br>";
        echo "<br><em>Sample parking slots have been created. You can manage slots through the admin panel.</em><br>";
    }

} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>