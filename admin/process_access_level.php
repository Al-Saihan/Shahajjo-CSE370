<?php
require_once '../includes/config.php';
session_start();

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

$fetched_admin_id = $_POST['user_id'] ?? null;
echo "User Id = {$_POST['user_id']}";

if (!$fetched_admin_id) {
    die("User ID is required.");
}

// Get all users with complete details
try {
    $stmt = $pdo->query("
        SELECT admin_id, access_level
        FROM admin_table
    ");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Find the user with the given admin_id/user_id
$admin = null;
foreach ($admins as $a) {
    echo "Searching User Id = {$a['admin_id']}<br>";
    if ($a['admin_id'] == $fetched_admin_id) {
        if ($a['admin_id'] === '1') {
            header("Location: dashboard.php?error=Cannot unverify main super admin");
            exit();
        }
        echo "Matched with admin Id = {$a['admin_id']}<br>";
        $admin = $a;
        break;
    }
}

if (!$admin) {
    die("Admin not found.");
}
// Toggle the user's status
$new_access_level = ($admin['access_level'] === 'super_admin') ? 'moderator' : 'super_admin';

try {
    $stmt = $pdo->prepare("
    UPDATE admin_table
    SET access_level = :new_access_level
    WHERE admin_id = :user_id
    ");
    $stmt->execute([
        ':new_access_level' => $new_access_level,
        ':user_id' => $fetched_admin_id
    ]);

    echo "Admin status updated successfully.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
header("Location: dashboard.php");
exit();
