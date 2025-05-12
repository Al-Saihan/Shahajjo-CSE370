<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

session_start();

if ($_SESSION['role'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}

if (!isset($_SESSION['pending_donation'])) {
    header("Location: donate.php");
    exit();
}

$recipient_id = $_SESSION['pending_donation']['recipient_id'];
$amount = $_SESSION['pending_donation']['amount'];
$method = $_SESSION['pending_donation']['method'];

try {
    // Fetch donor ID
    $stmt = $pdo->prepare("SELECT id FROM donor_table WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $donorData = $stmt->fetch();
    $donor_id = $donorData['id'] ?? null;

    if (!$donor_id) {
        throw new Exception("Donor ID not found.");
    }

    // Start transaction
    $pdo->beginTransaction();

    // Get next donation number
    $nextDonationNo = 1;
    $maxStmt = $pdo->query("SELECT MAX(donation_no) AS max_no FROM total_donations");
    $maxRow = $maxStmt->fetch();
    if ($maxRow && $maxRow['max_no']) {
        $nextDonationNo = $maxRow['max_no'] + 1;
    }

    // Insert into total_donations
    $insert = $pdo->prepare("
        INSERT INTO total_donations (
            donor_id, recipient_id, donation_no, donations_amount, donation_date, confirmation
        ) VALUES (?, ?, ?, ?, NOW(), 0)
    ");
    $insert->execute([$donor_id, $recipient_id, $nextDonationNo, $amount]);

    // Insert into financial_donations table
            $finStmt = $pdo->prepare("
                INSERT INTO financial_donations 
                (payment_type, td_no) 
                VALUES (?, ?)
            ");
            $finStmt->execute([$method, $nextDonationNo]);

    $pdo->commit();

    // Clear session
    unset($_SESSION['pending_donation']);

    $_SESSION['message'] = "<div class='alert alert-success text-center mt-3'>Donation confirmed and submitted successfully!</div>";
    header("Location: donate.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['message'] = "<div class='alert alert-danger text-center mt-3'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    header("Location: donate.php");
    exit();
}
