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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <style>
        .profile-card {
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

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Donor ID: <?= $donor['id'] ?>
                </span>
                <a class="nav-link" href="../../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card profile-card mb-4">
                    <div class="card-body text-center">
                        <h4><?= htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']) ?></h4>
                        <p class="text-muted">Donor Profile</p>
                        <hr>
                        <p><strong>Member Since:</strong><br>
                            <?= date('F j, Y', strtotime($donor['registration_date'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <?php if (isset($_SESSION['profile_update'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['profile_update'] ?>
                    </div>
                    <?php unset($_SESSION['profile_update']); ?>
                <?php endif; ?>

                <div class="card profile-card">
                    <div class="card-header bg-primary text-white">
                        <h4>My Information</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="info-box mb-4">
                                <h5>Contact Information</h5>
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

                            <div class="info-box">
                                <h5>Address</h5>
                                <div class="mb-3">
                                    <textarea class="form-control" name="address" rows="4" required><?=
                                                                                                    htmlspecialchars($donor['address'] ?? '')
                                                                                                    ?></textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>