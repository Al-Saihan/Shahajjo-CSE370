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

// Initialize session variables
$_SESSION['register_errors'] = [];
$_SESSION['register_data'] = $_POST;

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['register_errors'][] = "Invalid request method";
    header("Location: register.php");
    exit();
}

// Get and validate form data
$first_name = trim($_POST['first_name'] ?? '');
$middle_name = trim($_POST['middle_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$user_type = $_POST['user_type'] ?? 'donor';
echo '' . $first_name . '' . $middle_name . '' . $last_name . '' . $email . '' . $password . '' . $user_type;
// Validate inputs
$errors = [];
if (empty($first_name)) $errors[] = "First name is required";
if (empty($last_name)) $errors[] = "Last name is required";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address";
if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
if (!in_array($user_type, ['donor', 'recipient'])) {
    $errors[] = "Invalid user type selected";
}

// Check if email exists
if (empty($errors)) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM user_table WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered";
        }
    } catch (PDOException $e) {
        $errors[] = "System error. Please try again later.";
        error_log("Email check error: " . $e->getMessage());
    }
}

// Return to form if errors
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header("Location: register.php");
    exit();
}

// Process registration
try {
    $pdo->beginTransaction();

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into user_table
    $stmt = $pdo->prepare("
        INSERT INTO user_table 
        (first_name, middle_name, last_name, email, password, role) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $role = $user_type;

    $stmt->execute([
        $first_name,
        $middle_name,
        $last_name,
        $email,
        $password_hash,
        $role // DONOR, RECIPIENT
    ]);

    // ! --------------------
    $user_id = $pdo->lastInsertId();

    // Insert into specific table
    if ($user_type == 'donor') {
        $stmt = $pdo->prepare("INSERT INTO donor_table (user_id) VALUES (?)");
    } elseif ($user_type == 'recipient') {
        $stmt = $pdo->prepare("INSERT INTO recipient_table (user_id) VALUES (?)");
    }

    $stmt->execute([$user_id]);
    // ! --------------------

    $pdo->commit();

    // Clear session data
    unset($_SESSION['register_errors']);
    unset($_SESSION['register_data']);

    // Set success message
    $_SESSION['registration_success'] = true;
    // sendResponse('200', 'TRUE', "registration_success");

    header("Location: login.php");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['register_errors'] = ["Registration failed. Please try again."];
    sendResponse('400', 'FALSE', $e->getMessage());
    error_log("Registration error: " . $e->getMessage());
    header("Location: register.php");
    exit();
}
function sendResponse($status, $success, $message, $data = null)
{
    http_response_code($status);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}
// Run this in a PHP file or interactive shell
