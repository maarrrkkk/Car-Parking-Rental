<?php
// Database setup script - Auto-run when DB/tables are missing

// Only show output if accessed directly
$showOutput = (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME']));

// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
} else {
    $env = [];
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
            FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");
    if ($showOutput) echo "Bookings table created.<br>";


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

    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (role, firstname, lastname, email, phone, password, status, email_verified)
                                VALUES (:role, :firstname, :lastname, :email, :phone, :password, :status, 1)");
        $stmt->execute([
            ':role' => 'admin',
            ':firstname' => $env['ADMIN_FIRSTNAME'] ?? 'System',
            ':lastname'  => $env['ADMIN_LASTNAME'] ?? 'Admin',
            ':email'     => $env['ADMIN_EMAIL'] ?? 'admin@example.com',
            ':phone'     => $env['ADMIN_PHONE'] ?? '0000000000',
            ':password'  => password_hash($env['ADMIN_PASSWORD'] ?? 'admin123', PASSWORD_BCRYPT),
            ':status'    => 'Active'
        ]);
        if ($showOutput) echo "Default admin user created.<br>";
    } else {
        if ($showOutput) echo "Admin user already exists.<br>";
    }

    if ($showOutput) {
        echo "<br><strong>Database setup completed successfully!</strong><br>";
        echo "You can now access the application at index.php<br>";
        echo "Admin login: admin@example.com / admin123<br>";
    }

} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>