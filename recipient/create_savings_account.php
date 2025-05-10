<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SESSION['role'] !== 'recipient') {
    header("Location: ../unauthorized.php");
    exit();
}

// Get recipient data
try {
    $stmt = $pdo->prepare("
        SELECT r.id, r.wallet 
        FROM recipient_table r
        WHERE r.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recipient = $stmt->fetch();

    if (!$recipient) {
        die("Recipient not found");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Check if account already exists
try {
    $stmt = $pdo->prepare("SELECT * FROM savings_account WHERE recipient_uid = ?");
    $stmt->execute([$recipient['id']]);
    $existingAccount = $stmt->fetch();

    if ($existingAccount) {
        $_SESSION['account_message'] = "You already have an account. Please click on 'My Account' to view it.";
        header("Location: profile.php");  // Redirect back to profile page
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// If no account exists, create one
try {
    // Get the current wallet balance
    $walletBalance = $recipient['wallet'];

    // Insert new savings account
    $stmt = $pdo->prepare("
        INSERT INTO savings_account (recipient_uid, money, created_at) 
        VALUES (?, ?, CURRENT_DATE)
    ");
    $stmt->execute([$recipient['id'], $walletBalance]);

    $_SESSION['account_success'] = "Savings account created successfully!";
    header("Location: savings_account.php");  // Go to account page after creation
    exit();
} catch (PDOException $e) {
    die("Failed to create savings account: " . $e->getMessage());
}
