<?php
// Session and access control
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

// Redirect if not donor
if ($_SESSION['role'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}

// Fetch donor data
try {
    $stmt = $pdo->prepare("
        SELECT u.*, d.*
        FROM user_table u
        JOIN donor_table d ON u.id = d.user_id
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $donor = $stmt->fetch();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Get recipient_id from GET
$recipient_id = isset($_GET['recipient_id']) ? (int) $_GET['recipient_id'] : null;

// Optional: Fetch recipient name for confirmation
$recipient_name = '';
if ($recipient_id) {
    try {
        $rStmt = $pdo->prepare("
            SELECT u.first_name, u.middle_name, u.last_name
            FROM user_table u
            JOIN recipient_table r ON u.id = r.user_id
            WHERE r.user_id = ?
        ");
        $rStmt->execute([$recipient_id]);
        $recipient = $rStmt->fetch();
        if ($recipient) {
            $recipient_name = trim($recipient['first_name'] . ' ' . $recipient['middle_name'] . ' ' . $recipient['last_name']);
        }
    } catch (PDOException $e) {
        $recipient_name = '';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make a Donation | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .custom-button {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Donor ID: <?= htmlspecialchars($donor['id']) ?>
                </span>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card profile-card text-center">
                    <div class="card-header bg-success text-white">
                        <h4>Make a Donation</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Thank you, <strong><?= htmlspecialchars($donor['first_name']) ?></strong>, for supporting Shahajjo.
                        </p>
                        <?php if ($recipient_id && $recipient_name): ?>
                            <p class="mb-4">
                                You are donating to <strong><?= htmlspecialchars($recipient_name) ?></strong>.
                            </p>
                        <?php else: ?>
                            <p class="text-danger">Recipient not found or missing.</p>
                        <?php endif; ?>

                        <!-- Donation form -->
                        <form method="POST" action="#">
                            <input type="hidden" name="recipient_id" value="<?= htmlspecialchars($recipient_id) ?>">

                            <div class="mb-3 text-start">
                                <label class="form-label">Amount (in BDT)</label>
                                <input type="number" class="form-control" name="amount" placeholder="Enter amount" min="1" required>
                            </div>

                            <div class="mb-4 text-start">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="method" required>
                                    <option value="">Select</option>
                                    <option value="bkash">bKash</option>
                                    <option value="nagad">Nagad</option>
                                    <option value="bank">Bank Transfer</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success custom-button" style="width: 150px; height: 60px; font-size: 1.7rem;">
                                Donate
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
