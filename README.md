# Car Parking Rental System

A web-based car parking rental system built with PHP, MySQL, and Bootstrap.

## Features

- User registration and authentication
- Admin dashboard for managing parking slots
- Client booking system with payment integration
- Email notifications for inquiries and password resets
- Receipt upload and management
- Responsive design

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)

### Setup Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/Car-Parking-Rental.git
   cd Car-Parking-Rental
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment Variables:**

   Copy the `.env` file and update the following settings:

   #### Base URL Configuration
   ```env
   # Change this to match your installation path
   # Examples: /Car-Parking-Rental, /parking, / (if installed in root)
   BASE_URL=/Car-Parking-Rental
   ```

   #### Database Configuration
   ```env
   DB_HOST=localhost
   DB_NAME=car_parking_rental_db
   DB_USER=your_mysql_username
   DB_PASS=your_mysql_password
   ```

   **Important:** Replace `your_mysql_username` and `your_mysql_password` with your actual MySQL database credentials. These are the credentials you use to connect to your MySQL server.

   - `DB_HOST`: Usually `localhost` if running on the same server
   - `DB_NAME`: The name of your database (will be created automatically)
   - `DB_USER`: Your MySQL username (e.g., `root` for local development)
   - `DB_PASS`: Your MySQL password

   #### Admin User Defaults
   ```env
   ADMIN_FIRSTNAME=Super
   ADMIN_LASTNAME=Admin
   ADMIN_EMAIL=admin@yourdomain.com
   ADMIN_PHONE=09123456789
   ADMIN_PASSWORD=Qwerty12345
   ```

   #### Email Configuration (Gmail SMTP)
   ```env
   SMTP_HOST=smtp.gmail.com
   SMTP_PORT=587
   SMTP_USERNAME=yourgmail@gmail.com
   SMTP_PASSWORD=your_app_password
   SMTP_ENCRYPTION=tls
   ```

4. **Set up the database:**

    The application will automatically create the database and tables when you first access `setup.php` in your browser.

    - Open your browser and navigate to: `http://yourdomain.com/setup.php`
    - The script will create all necessary tables and the default admin user
    - Default admin login: `admin@carparking.com` / `Qwerty12345`
    - **Note:** No default parking slots are created. You must add them manually through the admin dashboard after logging in.

## Collaborator Setup Guide

If you're working with a team or someone else wants to contribute to this project, here's what they need to do after cloning the repository:

### Step 1: Clone and Initial Setup
```bash
git clone https://github.com/yourusername/Car-Parking-Rental.git
cd Car-Parking-Rental
```

### Step 2: Set Up Environment Variables
```bash
# Copy the template file
cp .env.example .env

# Edit the .env file with your local settings
nano .env  # or use any text editor
```

**Configure these settings in `.env`:**
```env
# Base URL - Change this to match your installation path
BASE_URL=/Car-Parking-Rental

# Database settings (your local MySQL)
DB_HOST=localhost
DB_NAME=car_parking_rental_db
DB_USER=root  # your MySQL username
DB_PASS=      # your MySQL password

# Admin defaults (can keep as-is)
ADMIN_FIRSTNAME=Super
ADMIN_LASTNAME=Admin
ADMIN_EMAIL=admin@carparking.com
ADMIN_PHONE=09123456789
ADMIN_PASSWORD=Qwerty12345

# Email settings (optional for development)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=yourgmail@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_ENCRYPTION=tls
```

### Step 3: Install Dependencies
```bash
composer install
```

### Step 4: Set Up Web Server

**Option A: XAMPP (recommended for Windows):**
- Copy project to `C:\xampp\htdocs\Car-Parking-Rental`
- Start Apache and MySQL in XAMPP Control Panel

**Option B: Built-in PHP server (for development):**
```bash
php -S localhost:8000
```
Access at: `http://localhost:8000`

### Step 5: Database Initialization
- Open browser and go to: `http://localhost/Car-Parking-Rental/setup.php`
- The setup script automatically creates database, tables, and the default admin user
- **Note:** No default parking slots are created. You must add them manually through the admin dashboard.

### Step 6: Access the Application
- **Main site:** `http://localhost/Car-Parking-Rental/index.php`
- **Admin login:**
 - Email: `admin@carparking.com`
 - Password: `Qwerty12345`

### What Files You Get vs. Need to Create

**Files from Git:**
- ✅ All PHP code and assets
- ✅ `composer.json` and `composer.lock`
- ✅ `README.md` and documentation
- ✅ `.env.example` template

**Files You Create:**
- ❌ `.env` (copy from `.env.example`)
- ❌ `vendor/` (created by `composer install`)
- ❌ Database (created by setup.php)

## Email Configuration (Gmail)

### Getting Your Gmail App Password

Since Google has disabled less secure app access, you need to use an App Password to send emails through Gmail SMTP.

#### Step 1: Enable 2-Factor Authentication (2FA)
1. Go to your Google Account settings: https://myaccount.google.com/
2. Navigate to "Security" in the left sidebar
3. Under "Signing in to Google", click on "2-Step Verification"
4. Follow the steps to enable 2-factor authentication

#### Step 2: Generate App Password
1. After enabling 2FA, go back to the Security page
2. Under "Signing in to Google", click on "App passwords"
3. You might need to sign in again
4. Select "Mail" as the app and "Other (custom name)" as the device
5. Enter "Car Parking System" as the custom name
6. Click "Generate"
7. Copy the 16-character password that appears (ignore spaces)

#### Step 3: Configure in .env
```env
SMTP_USERNAME=yourgmail@gmail.com
SMTP_PASSWORD=your_16_character_app_password
```

**Important Notes:**
- The app password is different from your regular Gmail password
- Keep this password secure - it has full access to your Gmail account
- If you disable 2FA, app passwords will stop working
- You can generate multiple app passwords for different applications

## File Structure

```
├── api/                    # API endpoints
├── assets/                 # CSS, JS, images
├── config/                 # Database configuration
├── includes/               # PHP includes and authentication
├── pages/                  # HTML/PHP pages
├── vendor/                 # Composer dependencies
├── .env                    # Environment variables
├── composer.json           # PHP dependencies
├── index.php               # Main entry point
├── setup.php               # Database setup script
└── README.md               # This file
```

## Usage

### Admin Features
- Login with admin credentials
- Add/edit/delete parking slots
- View all bookings and receipts
- Manage users

### Client Features
- Register and login
- Browse available parking slots
- Book slots with payment
- Upload payment receipts
- View booking history

## Troubleshooting

### Database Connection Issues
- Ensure MySQL is running
- Check that the credentials in `.env` are correct
- Make sure the database user has proper permissions

### Email Not Sending
- Verify your Gmail app password is correct
- Check that 2FA is enabled on your Google account
- Ensure the SMTP settings in `.env` are correct

### File Upload Issues
- Check that the `assets/images/` directories are writable
- Ensure proper file permissions (755 for directories, 644 for files)

### .env File Parsing Error
If you encounter an error like "syntax error, unexpected '(' in .env on line 13":

1. **Delete the corrupted .env file:**
   ```bash
   rm .env
   ```

2. **Recreate .env from scratch:**
   ```bash
   # Copy from template
   cp .env.example .env

   # Or create manually with these commands:
   echo DB_HOST=localhost > .env
   echo DB_NAME=car_parking_rental_db >> .env
   echo DB_USER=root >> .env
   echo DB_PASS= >> .env
   echo. >> .env
   echo # admin user default credentials >> .env
   echo ADMIN_FIRSTNAME=Super >> .env
   echo ADMIN_LASTNAME=Admin >> .env
   echo ADMIN_EMAIL=admin@carparking.com >> .env
   echo ADMIN_PHONE=09123456789 >> .env
   echo ADMIN_PASSWORD=Qwerty12345 >> .env
   echo. >> .env
   echo # Email Configuration >> .env
   echo SMTP_HOST=smtp.gmail.com >> .env
   echo SMTP_PORT=587 >> .env
   echo SMTP_USERNAME=yourgmail@gmail.com >> .env
   echo SMTP_PASSWORD=your_app_password >> .env
   echo SMTP_ENCRYPTION=tls >> .env
   ```

3. **Edit with your actual credentials:**
   ```bash
   nano .env  # or use any text editor
   ```

4. **Test the configuration:**
   ```bash
   php -r "$env = parse_ini_file('.env'); echo 'Config loaded successfully';"
   ```

**Cause:** The .env file may contain invisible characters, encoding issues, or formatting problems that prevent PHP's `parse_ini_file()` function from reading it correctly.

## Security Notes

- Change default admin password after first login
- Use HTTPS in production
- Regularly update dependencies
- Keep your `.env` file secure and never commit it to version control

## License

This project is for educational purposes. Please ensure compliance with local laws and regulations when deploying.