<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: dashboard.php");
    exit();
}



$donation_row = $_POST['donation'];

// Validate required parameters
if (!isset($donation_row) || !isset($_POST['action'])) {
    $_SESSION['error'] = "Missing required parameters.";
    header("Location: dashboard.php");
    exit();
}

foreach ($donation_row as $key => $value) {
    echo "<p><strong>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . ":</strong> " . htmlspecialchars($value) . "</p>";
}

$action = $_POST['action'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // First, verify the donation exists and is pending
    $stmt = $pdo->prepare("SELECT confirmation FROM total_donations WHERE donation_no = ?");
    $stmt->execute([$donation_row["donation_no"]]);
    $donation = $stmt->fetch();

    if (!$donation) {
        $_SESSION['error'] = "Donation not found.";
        $pdo->rollBack();
        header("Location: dashboard.php");
        exit();
    }

    if ($donation['confirmation'] != 0) {
        $_SESSION['error'] = "This donation has already been processed.";
        $pdo->rollBack();
        header("Location: dashboard.php");
        exit();
    }

    // Process based on action
    if ($action === 'confirm') {
        $updateStmt = $pdo->prepare("UPDATE total_donations SET confirmation = 1 WHERE donation_no = ?");
        $updateStmt->execute([$donation_row['donation_no']]);
        $updateStmt2 = $pdo->prepare("UPDATE recipient_table SET wallet = wallet + ?, last_received = ? WHERE user_id = ?");
        $updateStmt2->execute([
            $donation_row['donations_amount'],
            $donation_row['donation_date'],
            $donation_row['recipient_uid']
        ]);
        $updateStmt3 = $pdo->prepare("UPDATE donor_table SET total_donations = total_donations + 1, last_donation = ? WHERE user_id = ?");
        $updateStmt3->execute([
            $donation_row['donation_date'],
            $donation_row['donor_uid']
        ]);

        $_SESSION['success'] = "Donation #" . htmlspecialchars($donation_row['donation_no']) . " has been successfully confirmed. Amount Added: " . htmlspecialchars($donation_row['donations_amount']) . ", Donation Date: " . htmlspecialchars($donation_row['donation_date']) . ", Recipient UID: " . htmlspecialchars($donation_row['recipient_uid']) . ".";
    } elseif ($action === 'reject') {
        $updateStmt = $pdo->prepare("UPDATE total_donations SET confirmation = 2 WHERE donation_no = ?");
        $updateStmt->execute([$donation_row['donation_no']]);
        $_SESSION['success'] = "Donation #" . htmlspecialchars($donation_row['donation_no']) . " has been successfully rejected. Donor and Recipient information were not updated.";
    } else {
        $_SESSION['error'] = "Invalid action specified.";
        $pdo->rollBack();
        header("Location: dashboard.php");
        exit();
    }

    // Commit transaction if everything went well
    $pdo->commit();
} catch (PDOException $e) {
    // Roll back transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    // Roll back transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

// Redirect back to donation management page
header("Location: dashboard.php");
exit();
