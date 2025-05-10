<?php
// Load config and access control
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

// Check if user is a donor
if ($_SESSION['role'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}

// Get donor ID from donor_table using user_id
try {
    $donorStmt = $pdo->prepare("SELECT id FROM donor_table WHERE user_id = ?");
    $donorStmt->execute([$_SESSION['user_id']]);
    $donorData = $donorStmt->fetch();

    if ($donorData) {
        $donor_id = $donorData['id'];
    } else {
        $donor_id = 'N/A'; // Fallback if donor not found
    }
} catch (PDOException $e) {
    die("Error fetching donor ID: " . $e->getMessage());
}

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
        .user-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-row {
            background-color: #f8f9fa;
        }
        .address-box {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
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
                Donor ID: <?= htmlspecialchars($donor_id) ?>
                </span>
                <a class="nav-link" href="../process_logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center">
                <thead>
                    <tr class="table-dark">
                        <th style="border: 1px solid #dee2e6;">ID</th>
                        <th style="border: 1px solid #dee2e6;">Name</th>
                        <th style="border: 1px solid #dee2e6;">Email</th>
                        <th style="border: 1px solid #dee2e6;">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recipients as $recipient): ?>
                    <tr data-bs-toggle="collapse" data-bs-target="#details-<?= $recipient['recipient_table_id'] ?>" aria-expanded="false" aria-controls="details-<?= $recipient['recipient_table_id'] ?>">
        <!-- Recipient Table ID -->
        <td><?= $recipient['recipient_table_id'] ?></td>

        <!-- Full Name with optional middle name -->
        <td>
            <?= htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']) ?>
            <?php if (!empty($recipient['middle_name'])): ?>
                <br><small class="text-muted"><?= htmlspecialchars($recipient['middle_name']) ?></small>
            <?php endif; ?>
        </td>

        <!-- Email -->
        <td><?= htmlspecialchars($recipient['email']) ?></td>

        <!-- Action: Donate Button -->
        <td>
            <form action="donation.php" method="GET" class="d-inline">
                <input type="hidden" name="recipient_id" value="<?= $recipient['recipient_id'] ?>">
                <button type="submit" class="btn btn-sm btn-success border border-dark">Donate</button>
            </form>
        </td>
    </tr>
    <tr class="detail-row">
    <td colspan="7" class="p-0">
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
                            <?= $recipient['recipient_address']
                                ? nl2br(htmlspecialchars($recipient['recipient_address']))
                                : '<p class="text-muted">Not provided</p>' ?>
                        </div>
                        <div class="address-box">
                            <p><strong>Phone Number:</strong></p>
                            <?= $recipient['recipient_contact']
                                ? htmlspecialchars($recipient['recipient_contact'])
                                : '<span class="text-muted">Not provided</span>' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>
<?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
