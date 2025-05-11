<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SESSION['role'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}

// Get donor data
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

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['total_income'])) {
    $income = $_POST['total_income'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact_number'] ?? '';

    try {
        $stmt = $pdo->prepare("
            UPDATE donor_table 
            SET total_income = ?, address = ?, contact_number = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$income, $address, $contact, $_SESSION['user_id']]);

        $stmt = $pdo->prepare("UPDATE user_table SET profile_complete = TRUE WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);

        $_SESSION['profile_update'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to update profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Donor Profile | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .needs-box {
            background: rgba(112, 156, 206, 0.82);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        /* Rating Modal Styles */
        #ratingModal .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }

        #ratingModal .form-select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid rgba(61, 17, 55, 0.3);
        }

        #ratingModal textarea {
            min-height: 120px;
            border-radius: 8px;
            border: 1px solid rgba(61, 17, 55, 0.3);
        }

        .custom-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body style="background: linear-gradient(to right,rgb(135, 165, 209),rgba(4, 28, 46, 0.51));"></body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                Donor ID: <?= $donor['id'] ?>
            </span>
            <a class="nav-link" href="../process_logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card profile-card mb-4">
                <div class="card-body text-center">
                    <h4><?= htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']) ?></h4>
                    <p class="text-muted">Donor Profile</p>
                    <hr>
                    <p><strong>Member Since:</strong><br>
                        <?= date('F j, Y', strtotime($donor['registration_date'])) ?>
                    </p>
                </div>
            </div>

            <!-- Donate Button -->
            <div class="card profile-card mb-4 bg">
                <div class="card-body text-center">
                    <a href="donate.php" class="btn btn-success custom-card" style="width: 150px; height: 60px; font-size: 1.7rem;">
                        Donate
                    </a>
                </div>
            </div>
            <!-- Minimum Zakat Amount and Donate Zakat Buttons - Stacked Vertically -->
             <div class="card profile-card mb-4 bg-light">
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center gap-2">
                    <!-- Minimum Zakat Button -->
                     <button type="button" class="btn btn-success fw-bold w-50" data-bs-toggle="modal" data-bs-target="#zakatModal">
                         Minimum Zakat Amount
                        </button>
            <!-- Donate Zakat Button -->
             <button type="button" class="btn btn-success fw-bold w-50" data-bs-toggle="modal" data-bs-target="#donateZakatModal">
                Donate Zakat
            </button>
        </div>
    </div>
            <!-- Rate Us Button -->
            <div class="card profile-card mb-4 bg">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-pp btn-bg fw-bold" data-bs-toggle="modal" data-bs-target="#ratingModal">
                        Rate us!
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Feedback success message (only one instance) -->
            <?php if (isset($_SESSION['feedback_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <?= htmlspecialchars($_SESSION['feedback_success']) ?>
                </div>
                <?php unset($_SESSION['feedback_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['profile_update'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['profile_update']) ?>
                </div>
                <?php unset($_SESSION['profile_update']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['account_message'])): ?>
                <div class="alert alert-info">
                    <?= htmlspecialchars($_SESSION['account_message']) ?>
                </div>
                <?php unset($_SESSION['account_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['feedback_error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['feedback_error']) ?>
                </div>
                <?php unset($_SESSION['feedback_error']); ?>
            <?php endif; ?>

            <div class="card profile-card">
                <div class="card-header bg-primary text-white">
                    <h4>My Profile</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="needs-box mb-4 bg-dp">
                            <h5>Personal Information</h5>
                            <div class="mb-3">
                                <label class="form-label">Total Income (yearly)</label>
                                <input type="number" class="form-control" name="total_income"
                                    value="<?= htmlspecialchars($donor['total_income'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($donor['email']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" name="contact_number"
                                    value="<?= htmlspecialchars($donor['contact_number'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="needs-box bg-dp">
                            <h5>Address Details</h5>
                            <div class="mb-3">
                                <textarea class="form-control" name="address" rows="2" required><?=
                                                                                                htmlspecialchars($donor['address'] ?? '')
                                                                                                ?></textarea>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary mt-3 fw-bold">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Donate Zakat Modal with Amount Input -->
<div class="modal fade" id="donateZakatModal" tabindex="-1" aria-labelledby="donateZakatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="../donor/donate.php">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="donateZakatModalLabel">Donate Zakat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $zakat_threshold = 1139505.49;
                    $income = floatval($donor['total_income'] ?? 0);
                    $minimum_zakat = $income * 0.025;
                    ?>
                    
                    <?php if ($income >= $zakat_threshold): ?>
                        <div class="mb-3">
                            <label for="zakatAmount" class="form-label">Enter Zakat Amount (Minimum: ৳<?= number_format($minimum_zakat, 2) ?>)</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" class="form-control" id="zakatAmount" name="amount" 
                                    min="<?= $minimum_zakat ?>" step="0.01" required
                                    placeholder="Enter amount">
                            </div>
                            <small class="text-muted">Your minimum zakat amount is 2.5% of your yearly income.</small>
                        </div>
                    <?php elseif ($income > 0): ?>
                        <div class="alert alert-danger">
                            You are not eligible to pay zakat as your yearly income (৳<?= number_format($income, 2) ?>)
                            is below the nisab threshold of ৳<?= number_format($zakat_threshold, 2) ?>.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Please update your profile with your total income to check zakat eligibility.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <?php if ($income >= $zakat_threshold): ?>
                        <button type="submit" class="btn btn-primary">Proceed to Donate</button>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="zakat_donation" value="1">
            </form>
        </div>
    </div>
</div>

<!-- Rating popup(modal) -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="ratingModalLabel">Rate Our Service</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="process_feedback.php">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Rating (1-5 stars)</label>
                        <select class="form-select" name="stars" required>
                            <option value="">Select rating</option>
                            <option value="1">★☆☆☆☆ (1 star - Poor)</option>
                            <option value="2">★★☆☆☆ (2 stars - Fair)</option>
                            <option value="3">★★★☆☆ (3 stars - Good)</option>
                            <option value="4">★★★★☆ (4 stars - Very Good)</option>
                            <option value="5">★★★★★ (5 stars - Excellent)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="feedbackText" class="form-label">Your Feedback</label>
                        <textarea class="form-control" id="feedbackText" name="feedback" rows="3"
                            placeholder="Please share your experience with us..." required></textarea>
                    </div>
                    <input type="hidden" name="submit_feedback" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-pp">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Zakat Modal -->
<div class="modal fade" id="zakatModal" tabindex="-1" aria-labelledby="zakatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-dark">
                <h5 class="modal-title" id="zakatModalLabel">Zakat Eligibility & Amount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $zakat_threshold = 1139505.49;
                $income = floatval($donor['total_income'] ?? 0);
                ?>
                <?php if ($income >= $zakat_threshold): ?>
                    <p class="mb-3">
                        ✅ You are eligible for zakat based on your yearly income of <strong>৳<?= number_format($income, 2) ?></strong>.
                    </p>
                    <p>
                        💰 Minimum zakat amount (2.5% of income): <br>
                        <strong>৳<?= number_format($income * 0.025, 2) ?></strong>
                    </p>
                <?php elseif ($income > 0): ?>
                    <p class="text-danger">
                        ❌ You are <strong>not eligible</strong> for zakat based on your yearly income of <strong>৳<?= number_format($income, 2) ?></strong>.
                    </p>
                    <p>The required minimum for zakat eligibility is <strong>৳<?= number_format($zakat_threshold, 2) ?></strong>.</p>
                <?php else: ?>
                    <p class="text-muted">
                        ℹ️ Please update your profile with your total income to check zakat eligibility.
                    </p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
</body>

</html>