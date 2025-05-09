<?php
require_once '../includes/config.php';
session_start();

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_POST['user_id'] ?? null;
echo "User Id = {$_POST['user_id']}";

if (!$user_id) {
    die("User ID is required.");
}

// Get all users with complete details
try {
    $stmt = $pdo->query("
        SELECT id, status, role
        FROM user_table
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

echo "{$users[0]['status']}<br>";

// Find the user with the given user_id
$user = null;
foreach ($users as $u) {
    echo "Searching User Id = {$u['id']}<br>";
    if ($u['id'] == $user_id) {
        if ($u['role'] === 'admin') {
            header("Location: dashboard.php?error=Cannot unverify admin");
            exit();
        }
        echo "Matched with User Id = {$u['id']}<br>";
        $user = $u;
        break;
    }
}

if (!$user) {
    die("User not found.");
}
// Toggle the user's status
$new_status = ($user['status'] === 'verified') ? 'unverified' : 'verified';

try {
    $stmt = $pdo->prepare("
    UPDATE user_table
    SET status = :new_status
    WHERE id = :user_id
    ");
    $stmt->execute([
        ':new_status' => $new_status,
        ':user_id' => $user_id
    ]);

    echo "User status updated successfully.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
header("Location: dashboard.php");
exit();
