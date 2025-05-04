<?php
require_once 'includes/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['changepass_error'] = "Invalid request method";
    header("Location: changepass.php");
    exit();
}

// Get form data
$email = trim($_POST['email'] ?? '');
$newpassword = $_POST['newpassword'] ?? '';

// Basic validation
if (empty($email) || empty($newpassword)) {
    $_SESSION['changepass_error'] = "Email and password are required";
    header("Location: changepass.php");
    exit();
}

// Validate password 
if (strlen($newpassword) < 8) {
    $_SESSION['changepass_error'] = "Password must be at least 8 characters long";
    header("Location: changepass.php");
    exit();
}

try {
    // Check user exists
    $stmt = $pdo->prepare("SELECT id FROM user_table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['changepass_error'] = "No account found with that email";
        header("Location: changepass.php");
        exit();
    }

    // Encrypt the password
    $hashed_password = password_hash($newpassword, PASSWORD_DEFAULT);

    // Update password in the database
    $stmt = $pdo->prepare("UPDATE user_table SET password = ? WHERE email = ?");
    $stmt->execute([$hashed_password, $email]);

    // Password updated successfully
    $_SESSION['changepass_success'] = "Password updated successfully. You can now log in.";

    // Clear any existing auth sessions for security
    unset($_SESSION['user_id']);
    unset($_SESSION['role']);

    header("Location: login.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['changepass_error'] = "An error occurred. Please try again.";
    error_log("Change password error: " . $e->getMessage());
    header("Location: changepass.php");
    exit();
}
