<?php
session_start();
require_once __DIR__ . '/includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $stmt = $conn->prepare("SELECT UID, F_name, L_Name, Password, user_type FROM User_table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['UID'];
            $_SESSION['f_name'] = $user['F_name'];
            $_SESSION['l_name'] = $user['L_Name'];
            $_SESSION['user_type'] = $user['user_type'];

            // Define base URL before using it
            $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/donation_system";

            // Redirect based on user type
            switch ($_SESSION['user_type']) {
                case 'donor':
                    header("Location: $base_url/donor/profile.php");
                    exit();
                case 'recipient':
                    header("Location: $base_url/recipient/profile.php");
                    exit();
                default:
                    header("Location: $base_url/index.php");
                    exit();
            }
        }
    }

    $_SESSION['error'] = "Invalid email or password";
    header("Location: login.php");
    exit();
}
?>
