<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: savings_account.php");
    exit();
}

if ($_SESSION['role'] !== 'recipient') {
    header("Location: ../unauthorized.php");
    exit();
}

// Validate inputs
$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$accountNo = filter_input(INPUT_POST, 'account_no', FILTER_VALIDATE_INT);
$paymentMethod = $_POST['payment_method'] ?? '';

if (!$amount || $amount <= 0 || !$accountNo || empty($paymentMethod)) {
    $_SESSION['deposit_error'] = "Invalid deposit data";
    header("Location: savings_account.php");
    exit();
}

try {
    // Update savings account balance
    $stmt = $pdo->prepare("
        UPDATE savings_account 
        SET money = money + ? 
        WHERE account_no = ?
    ");
    $stmt->execute([$amount, $accountNo]);
    
    $_SESSION['deposit_success'] = "Deposit of ৳" . number_format($amount, 2) . " was successful!";
    header("Location: savings_account.php");
    exit();
    
} catch (PDOException $e) {
    $_SESSION['deposit_error'] = "Deposit failed: " . htmlspecialchars($e->getMessage());
    header("Location: savings_account.php");
    exit();
}