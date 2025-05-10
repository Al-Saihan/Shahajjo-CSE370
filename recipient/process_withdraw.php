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
    $_SESSION['withdraw_error'] = "Invalid withdrawal data";
    header("Location: savings_account.php");
    exit();
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Get current balance
    $stmt = $pdo->prepare("SELECT money FROM savings_account WHERE account_no = ?");
    $stmt->execute([$accountNo]);
    $account = $stmt->fetch();

    if (!$account) {
        $pdo->rollBack();
        $_SESSION['withdraw_error'] = "Account not found.";
        header("Location: savings_account.php");
        exit();
    }

    $currentBalance = $account['money'];

    if ($currentBalance < $amount) {
        $pdo->rollBack();
        $_SESSION['withdraw_error'] = "Insufficient funds.";
        header("Location: savings_account.php");
        exit();
    }

    // Deduct amount from balance
    $stmt = $pdo->prepare("UPDATE savings_account SET money = money - ? WHERE account_no = ?");
    $stmt->execute([$amount, $accountNo]);

    // Log withdrawal as approved/completed
    $stmt = $pdo->prepare("
        INSERT INTO savings_withdrawal_log (account_no, amount, withdrawal_method, status)
        VALUES (?, ?, ?, 'approved')
    ");
    $stmt->execute([$accountNo, $amount, $paymentMethod]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['withdraw_success'] = "Withdrawal of ৳" . number_format($amount, 2) . " completed successfully.";
    header("Location: savings_account.php");
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['withdraw_error'] = "Withdrawal failed: " . htmlspecialchars($e->getMessage());
    header("Location: savings_account.php");
    exit();
}
