<?php
require_once __DIR__ . '/includes/paths.php';
require_once CONFIG_PATH;
require_once AUTH_PATH;

requireLogin();

$redirects = [
    'donor' => ROOT_PATH . '/donor/profile.php',
    'recipient' => ROOT_PATH . '/recipient/profile.php'
];

if (isset($redirects[$_SESSION['user_type']])) {
    header("Location: " . $redirects[$_SESSION['user_type']]);
} else {
    header("Location: " . ROOT_PATH . "/login.php");
}
exit();
?>