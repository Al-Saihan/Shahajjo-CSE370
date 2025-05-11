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
        SELECT u.*, r.* 
        FROM user_table u
        JOIN recipient_table r ON u.id = r.user_id
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recipient = $stmt->fetch();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['income'])) {
    $income = $_POST['income'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact_number'] ?? '';
    $cause = $_POST['cause'] ?? '';

    try {
        $stmt = $pdo->prepare("
            UPDATE recipient_table 
            SET income = ?, address = ?, contact_number = ?, cause = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$income, $address, $contact, $cause, $_SESSION['user_id']]);
        
        // Mark profile as complete
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
    <title>Recipient Profile | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .needs-box {
            background:rgba(207, 164, 164, 0.82);
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
        .bg-sakib {
            background-color:rgba(60, 50, 65, 0.6);
        }
        .bg-pink {
            background-color:rgba(255, 255, 255, 0.24);
        }
        .bg-dark_pink {
            background-color:rgba(61, 17, 55, 0.58); 
        }
        .bg-dp {
            background-color:rgba(61, 17, 55, 0.35); 
        }
        .btn-pp {
            background-color:rgba(61, 17, 55, 0.35);
        }
        .text-taka {
            color: rgb(21, 143, 19); 
            font-weight: bold;
        }
    </style>
</head>
<body style="background: linear-gradient(to right,rgb(216, 196, 215),rgba(78, 82, 85, 0.51));">

    <nav class="navbar navbar-expand-lg navbar-dark bg-sakib">
        <div class="container"> 
            <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Recipient ID: <?= htmlspecialchars($recipient['id']) ?>
                </span>
                <a class="nav-link" href="../process_logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card profile-card mb-4 bg-pink">
                    <div class="card-body text-center">
                        <h4><?= htmlspecialchars($recipient['first_name'].' '.$recipient['last_name']) ?></h4>
                        <p class="text-muted">Recipient Profile</p>
                        <hr>
                        <p><strong>Member Since:</strong><br>
                        <?= date('F j, Y', strtotime($recipient['registration_date'])) ?></p>
                    </div>
                </div>

                <div class="card profile-card mb-4 bg-pink">
                    <div class="card-body text-center">
                        <h4><?= htmlspecialchars($recipient['first_name'] . "'s") ?></h4>
                        <p class="fw-bold fs-10 text-taka">Wallet                       
                        <hr>
                        <p class="fw-bold fs-1 text-taka">৳
                        <?= number_format($recipient['wallet'], 2) ?></p>
                        <hr>
                        <p class="fw-bold fs-20 text-dark">Last Received
                        <?php if ($recipient['last_received'] === null): ?>
                        <h4>---</h4>
                        <?php else: ?>
                            <h4><?= htmlspecialchars($recipient['last_received']) ?></h4>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card profile-card mb-4 bg-pink">
                    <div class="card-body text-center">                       
                        <a href="savings_account.php" class="btn btn-pp btn-bg fw-bold">My Account</a>
                        <hr>
                        <a href="create_savings_account.php" class="btn btn-pp btn-bg fw-bold">Create an Account</a>                           
                    </div>
                </div>
                <div class="card profile-card mb-4 bg-pink">
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

                <div class="card profile-card bg-pink">
                    <div class="card-header bg-dark_pink text-light">
                        <h4>My Profile</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="needs-box mb-4 bg-dp">
                                <h5>Personal Information</h5>
                                <div class="mb-3">
                                    <label class="form-label">Total Income (yearly)</label>
                                    <input type="number" class="form-control" name="income" 
                                           value="<?= htmlspecialchars($recipient['income'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?= htmlspecialchars($recipient['email']) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" 
                                           value="<?= htmlspecialchars($recipient['contact_number'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="needs-box bg-dp">
                                <h5>Address Details</h5>
                                <div class="mb-3">
                                    <textarea class="form-control" name="address" rows="2" required><?= 
                                        htmlspecialchars($recipient['address'] ?? '') 
                                    ?></textarea>
                                </div>
                            </div>
                            <div class="needs-box bg-dp">
                                <h5>Add your cause</h5>
                                <div class="mb-3">
                                    <textarea class="form-control" name="cause" rows="4" required><?= 
                                        htmlspecialchars($recipient['cause'] ?? '') 
                                    ?></textarea>
                                </div>
                            </div>
                            <hr>                        
                            <button type="submit" class="btn btn-pp mt-3 fw-bold">Update Profile</button>                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating popup(modal) -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark_pink text-white">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>