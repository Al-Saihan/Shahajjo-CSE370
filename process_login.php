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
    $_SESSION['login_error'] = "Invalid request method";
    header("Location: login.php");
    exit();
}

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Email and password are required";
    header("Location: login.php");
    exit();
}

try {
    // Check user exists
    $stmt = $pdo->prepare("SELECT * FROM user_table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(); // returns the entire user row i think

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }

    // Set session data
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role']; // 'donor', 'recipient', or 'admin'
    $_SESSION['user_type'] = $user['user_type'];

    // Redirect based on role
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'recipient':
            header("Location: recipient/profile.php");
            break;
        case 'donor':
            //default:
            header("Location: donor/profile.php");
            echo "hoise donor";
            break;
    }
    exit();
} catch (PDOException $e) {
    $_SESSION['login_error'] = "Login failed. Please try again.";
    error_log("Login error: " . $e->getMessage());
    header("Location: login.php");
    exit();
}
