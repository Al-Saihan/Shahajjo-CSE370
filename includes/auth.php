<?php
// Authentication Functions
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

function requireRole($role)
{
    requireLogin();
    if ($_SESSION['user_type'] !== $role) {
        header("Location: unauthorized.php");
        exit();
    }
}

function getCurrentUser()
{
    global $conn;
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM User_table WHERE UID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}
