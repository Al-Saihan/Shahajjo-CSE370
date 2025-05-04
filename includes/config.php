<?php
$host = 'localhost';
$dbname = 'shahajjo_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Database connected successfully!"; // Add this line temporarily
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
