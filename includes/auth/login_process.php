<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Find user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // ======================
        // Check last login logic
        // ======================
        $now = new DateTime();
        $lastLogin = !empty($user['last_login']) ? new DateTime($user['last_login']) : null;

        // Default: Active if new user or logged in within the last 7 days
        $status = "Active";
        if ($lastLogin) {
            $diff = $lastLogin->diff($now);
            if ($diff->days > 7) {
                $status = "Inactive"; // more than 1 week inactive
            }
        }

        // Update last_login and status in DB
        $update = $pdo->prepare("UPDATE users SET last_login = NOW(), status = :status WHERE id = :id");
        $update->execute([
            'status' => $status,
            'id' => $user['id']
        ]);

        // Save session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['firstname'] . " " . $user['lastname'];
        $_SESSION['role'] = $user['role']; // Save role in session

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../../pages/admin/index.php");
        } else {
            header("Location: ../../index.php");
            // Or home.php if that's your client homepage
        }
        exit;
    } else {
        // pass email back to form
        header("Location: ../../index.php?page=login&error=1&email=" . urlencode($email));
        exit;
    }
}
