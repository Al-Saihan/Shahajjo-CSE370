<?php
// This must be included at the very top of every restricted page
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// For role-specific pages, add additional checks like:
/*
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}
*/
