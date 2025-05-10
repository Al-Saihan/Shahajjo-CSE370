<?php
// Updated path to config.php (one level up from donor folder)
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['income'])) {
    $income = $_POST['income'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact_number'] ?? '';
    
    try {
        $stmt = $pdo->prepare("
            UPDATE donor_table 
            SET address = ?, contact_number = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$address, $contact, $_SESSION['user_id']]);
        
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
    <title>Donor Profile | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .needs-box {
            background:rgba(32, 144, 185, 0.82);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .custom-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
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
                    <h4><?= htmlspecialchars($donor['first_name'].' '.$donor['last_name']) ?></h4>
                    <p class="text-muted">Donor Profile</p>
                    <hr>
                    <p><strong>Member Since:</strong><br>
                        <?= date('F j, Y', strtotime($donor['registration_date'])) ?>
                    </p>
                </div>
            </div>

            <!-- Donate Button -->
            <div class="text-start px-2">
            <div class="card-body text-center">
                <a href="donate.php" class="btn btn-success custom-card" style="width: 150px; height: 60px; font-size: 1.7rem;">
                    Donate
                </a>
                </div>
            </div>
            <!-- Rate Us Button -->
            <div class="text-start px-2 mt-3">
                <div class="card-body text-center">
                    <button class="btn btn-warning custom-card" style="width: 150px; height: 60px; font-size: 1.7rem;" data-bs-toggle="modal" data-bs-target="#rateUsModal">
                        Rate Us
                    </button>
                </div>
            </div>
        </div>
            <div class="col-md-8">
                <?php if (isset($_SESSION['profile_update'])): ?>
                <?php if (isset($_SESSION['feedback_success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['feedback_success']; ?></div>
                    <?php unset($_SESSION['feedback_success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['feedback_error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['feedback_error']; ?></div>
                    <?php unset($_SESSION['feedback_error']); ?>
                <?php endif; ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['profile_update'] ?>
                    </div>
                    <?php unset($_SESSION['profile_update']); ?>
                <?php endif; ?>

                <div class="card profile-card">

                    <?php if (isset($_SESSION['feedback_success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['feedback_success']; ?></div>
                        <?php unset($_SESSION['feedback_success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['feedback_error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['feedback_error']; ?></div>
                        <?php unset($_SESSION['feedback_error']); ?>
                    <?php endif; ?>

                    <div class="card-header bg-primary text-white">
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
                                    <textarea class="form-control" name="address" rows="4" required><?= 
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

    
        <!-- Rate Us Modal -->
    <div class="modal fade" id="rateUsModal" tabindex="-1" aria-labelledby="rateUsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="process_feedback.php" method="POST" class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="rateUsModalLabel">Rate Shahajjo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
            <label class="form-label">Your Rating (1 to 5)</label>
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
            <label class="form-label">Feedback</label>
            <textarea class="form-control" name="feedback" rows="4" placeholder="Write your thoughts here..." required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
        </form>
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