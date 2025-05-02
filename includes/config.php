<?php
require_once __DIR__ . '/paths.php';

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "shahajjo_db";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitization function
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
    }
}
?>