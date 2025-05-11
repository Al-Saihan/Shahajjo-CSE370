<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipient_id'], $_POST['amount'], $_POST['method'])) {
    $_SESSION['pending_donation'] = [
        'recipient_id' => $_POST['recipient_id'],
        'amount' => $_POST['amount'],
        'method' => $_POST['method']
    ];
} else {
    header("Location: donate.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm Donation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>Confirm Your Donation</h4>
        </div>
        <div class="card-body">
            <p><strong>Recipient ID:</strong> <?= htmlspecialchars($_SESSION['pending_donation']['recipient_id']) ?></p>
            <p><strong>Amount:</strong> <?= htmlspecialchars($_SESSION['pending_donation']['amount']) ?> BDT</p>
            <p><strong>Method:</strong> <?= htmlspecialchars(ucfirst($_SESSION['pending_donation']['method'])) ?></p>
            <form method="POST" action="donate_confirm_process.php">
                <button type="submit" class="btn btn-success">✅ Confirm Donation</button>
                <a href="donate.php" class="btn btn-secondary">❌ Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
