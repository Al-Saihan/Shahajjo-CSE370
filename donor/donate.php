<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SESSION['role'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}

// Fetch donor ID
try {
    $donorStmt = $pdo->prepare("SELECT id FROM donor_table WHERE user_id = ?");
    $donorStmt->execute([$_SESSION['user_id']]);
    $donorData = $donorStmt->fetch();
    $donor_id = $donorData ? $donorData['id'] : 'N/A';
} catch (PDOException $e) {
    die("Error fetching donor ID: " . $e->getMessage());
}

// Handle donation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient_id = $_POST['recipient_id'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $method = $_POST['method'] ?? null;

    if ($recipient_id && $amount && $method) {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Get next donation number
            $nextDonationNo = 1;
            $maxStmt = $pdo->query("SELECT MAX(donation_no) AS max_no FROM total_donations");
            $maxRow = $maxStmt->fetch();
            if ($maxRow && $maxRow['max_no']) {
                $nextDonationNo = $maxRow['max_no'] + 1;
            }

            // Insert donation record
            $insert = $pdo->prepare("
                INSERT INTO total_donations (
                    donor_id, recipient_id, donation_no, donations_amount, donation_date, confirmation
                ) VALUES (?, ?, ?, ?, NOW(), 0)
            ");
            $insert->execute([$donor_id, $recipient_id, $nextDonationNo, $amount]);

            /// Update recipient's wallet and last_received date
            $updateRecipient = $pdo->prepare("
                UPDATE recipient_table
                SET wallet = wallet + ?, last_received = CURDATE()
                WHERE id = ?
            ");
            $updateRecipient->execute([$amount, $recipient_id]);

            // Commit transaction
            $pdo->commit();

            $message = "<div class='alert alert-success text-center mt-3'>Donation submitted successfully!</div>";
        } catch (Exception $e) {
            // Only rollback if a transaction is active
                $pdo->rollBack();
                $_SESSION['message'] = "<div class='alert alert-danger text-center mt-3'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
                header("Location: donate.php");
                exit();
        }
    } else {
        $_SESSION['message'] = "<div class='alert alert-success text-center mt-3'>Donation submitted successfully!</div>";
        header("Location: donate.php");
        exit();
    }
}

// Fetch verified recipients
try {
    $stmt = $pdo->query("
        SELECT
            r.id AS recipient_table_id,
            u.id, u.first_name, u.middle_name, u.last_name, u.email, u.status,
            r.address AS recipient_address, r.contact_number AS recipient_contact, r.user_id AS recipient_id,
            r.wallet AS wallet, r.cause AS cause
        FROM user_table u
        LEFT JOIN recipient_table r ON u.id = r.user_id
        WHERE u.status = 'verified' AND u.role = 'recipient'
    ");
    $recipients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Donate | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-details { background-color: #f8f9fa; border-radius: 5px; padding: 15px; margin-top: 10px; }
        .address-box { background-color: white; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; margin-bottom: 10px; }
        .modal-header h5 { margin: 0; }
    </style>
</head>
<body style="background: linear-gradient(to right,rgba(4, 28, 46, 0.51),rgb(135, 165, 209));"></body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">Donor ID: <?= htmlspecialchars($donor_id) ?></span>
            <a class="nav-link" href="../process_logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover text-center">
            <thead>
            <tr class="table-dark">
                <th>Recipient ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recipients as $recipient): ?>
                <tr data-bs-toggle="collapse" data-bs-target="#details-<?= $recipient['recipient_table_id'] ?>" aria-expanded="false">
                    <td><?= $recipient['recipient_table_id'] ?></td>
                    <td>
                        <?= htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']) ?>
                        <?php if (!empty($recipient['middle_name'])): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($recipient['middle_name']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($recipient['email']) ?></td>
                    <td>
                        <!-- Modal Trigger -->
                        <button class="btn btn-sm btn-success border border-dark" data-bs-toggle="modal" data-bs-target="#donateModal<?= $recipient['recipient_table_id'] ?>" onclick="event.stopPropagation();">
                            Donate
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="p-0">
                        <div id="details-<?= $recipient['recipient_table_id'] ?>" class="collapse">
                            <div class="user-details p-4 border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Basic Information</h5>
                                        <div class="address-box">
                                            <p><strong>Full Name:</strong>
                                                <?= htmlspecialchars($recipient['first_name'] . ' ' .
                                                    ($recipient['middle_name'] ? $recipient['middle_name'] . ' ' : '') .
                                                    $recipient['last_name']) ?>
                                            </p>
                                            <p><strong>Email:</strong> <?= htmlspecialchars($recipient['email']) ?></p>
                                            <p><strong>Wallet:</strong>
                                                <?= $recipient['wallet'] ? htmlspecialchars($recipient['wallet']) : '<span class="text-muted">Not provided</span>' ?>
                                            </p>
                                            <p><strong>Cause:</strong>
                                                <?= $recipient['cause'] ? htmlspecialchars($recipient['cause']) : '<span class="text-muted">Not provided</span>' ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Contact Information</h5>
                                        <div class="address-box">
                                            <p><strong>Address:</strong></p>
                                            <?= $recipient['recipient_address'] ? nl2br(htmlspecialchars($recipient['recipient_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                        </div>
                                        <div class="address-box">
                                            <p><strong>Phone Number:</strong></p>
                                            <?= $recipient['recipient_contact'] ? htmlspecialchars($recipient['recipient_contact']) : '<span class="text-muted">Not provided</span>' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="donateModal<?= $recipient['recipient_table_id'] ?>" tabindex="-1" aria-labelledby="donateModalLabel<?= $recipient['recipient_table_id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="donation_confirm.php">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="donateModalLabel<?= $recipient['recipient_table_id'] ?>">
                                        Donate to <?= htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']) ?>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="recipient_id" value="<?= $recipient['recipient_table_id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Amount (BDT)</label>
                                        <input type="number" name="amount" class="form-control" required min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Payment Method</label>
                                        <select name="method" class="form-select" required>
                                            <option value="">Select</option>
                                            <option value="bkash">bKash</option>
                                            <option value="nagad">Nagad</option>
                                            <option value="bank">Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Donate</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
