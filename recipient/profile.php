<?php
// Updated path to config.php (one level up from recipient folder)
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $contact = $_POST['contact_number'] ?? '';
    $cause = $_POST['cause'] ?? '';

    
    try {
        $stmt = $pdo->prepare("
            UPDATE recipient_table 
            SET address = ?, contact_number = ?, cause = ?
            WHERE user_id = ?
        ");
        $stmt->execute([$address, $contact, $cause, $_SESSION['user_id']]);
        
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
    </style>
</head>
<body style="background: linear-gradient(to right,rgb(151, 128, 149),rgb(204, 205, 206));">
<head>
    <style>
        .bg-sakib {
            background-color:rgba(60, 50, 65, 0.6); /* Custom color (Hex) */
        }
    </style>
</head>
    <nav class="navbar navbar-expand-lg navbar-dark bg-sakib">
        <div class="container"> 
            <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Recipient ID: <?= $recipient['id'] ?>
                </span>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
        <head>
                <style>
                    .bg-pink {
                        background-color:rgba(255, 255, 255, 0.24); /* Custom color (Hex) */
                    }
                </style>
            </head>
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
                        <p class="fw-bold fs-10 text-success">Wallet                       
                        <hr>
                        <p class="fw-bold fs-1 text-success">৳
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
                        <h4><?= htmlspecialchars($recipient['first_name'] . "'s") ?></h4>
                        <p class="fw-bold fs-10 text-success">Wallet                       
                        <hr>
                        <p class="fw-bold fs-1 text-success">৳
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

            </div>

            <div class="col-md-8">
                <?php if (isset($_SESSION['profile_update'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['profile_update'] ?>
                    </div>
                    <?php unset($_SESSION['profile_update']); ?>
                <?php endif; ?>
                <head>
                <style>
                    .bg-dark_pink {
                        background-color:rgba(61, 17, 55, 0.58); /* Custom color (Hex) */
                    }
                </style>
            </head>
                <div class="card profile-card bg-pink">
                    <div class="card-header bg-dark_pink text-light">
                        <h4>My Profile</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                        <head>
                            <style>
                                .bg-dp {
                                    background-color:rgba(61, 17, 55, 0.35); /* Custom color (Hex) */
                                }
                            </style>
                        </head>
                            <div class="needs-box mb-4 bg-dp">
                                <h5>Contact Information</h5>
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
                            <head>
                            <style>
                                .btn-pp {
                                    background-color:rgba(61, 17, 55, 0.35); /* Custom color (Hex) */
                                }
                            </style>
                        <hr>
                        </head>                         
                            <button type="submit" class="btn btn-pp mt-3">Update Profile</button>                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>