<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shahajjo - Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
        }
    </style>
</head>

<body style="background: linear-gradient(to right, rgba(78, 82, 85, 0.51), rgb(216, 196, 215));">
    <!-- Simplified Header without login/register -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand auth-logo fw-bold text-white fs-3" href="index.php" style="font-size: 1.5rem; letter-spacing: 2px;">Shahajjo</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="hero-section">
        <div class="container text-center">
            <h1>Welcome to Shahajjo</h1>
            <p class="lead">Our platform connects generous donors with recipients in need. Whether you want to make a financial contribution, donate essential items, or give Jakat, we provide a transparent and efficient way to make your donations count.</p>
            <div class="mt-4">
                <a href="register.php" class="btn btn-primary btn-lg me-2">Register</a>
                <a href="login.php" class="btn btn-outline-primary btn-lg">Login</a>
            </div>

            <!-- // ! NOTE -->
            <!-- // !? TEMPORARY BUTTONS FOR TESTING PURPOSES -- REMOVE THIS IN PRODUCTION -->
            <div class="mt-4">
                <a href="refreshDB.php" class="btn btn-danger btn-lg me-2" onclick="return confirm('Are You Sure You Want To Rebuild The Database With Dummy Users?');">Refresh Database<br>TEMPORARY</a>
            </div>
            <!-- // ? TEMPORARY BUTTONS FOR TESTING PURPOSES -- REMOVE THIS IN PRODUCTION -->
            <!-- // ! NOTE -->

        </div>
    </div>

    <!-- Feedback Section -->
    <div class="container text-center mt-5">
        <h2>Our Recent Feedbacks</h2>
        <div id="feedback-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                try {
                    $stmt = $pdo->prepare("
                        SELECT 
                            user_table.id AS user_id,
                            user_table.first_name,
                            user_table.middle_name,
                            user_table.last_name,
                            feedback_table.review,
                            feedback_table.stars,
                            feedback_table.posting_date
                        FROM 
                            feedback_table
                        INNER JOIN 
                            user_table ON feedback_table.uid = user_table.id
                        WHERE 
                            feedback_table.stars >= 3
                        LIMIT 5");
                    $stmt->execute();
                    $feedbacks = $stmt->fetchAll();

                    $isActive = true; // flages the first class as active for the carousel (Bootstrap Issue)
                    foreach ($feedbacks as $feedback) {
                        echo '<div class="carousel-item' . ($isActive ? ' active' : '') . '">';
                        echo '<div class="d-block w-100 p-4 bg-light rounded">';
                        echo '<p class="mb-1"> ' . htmlspecialchars($feedback['first_name'] . ' ' . ($feedback['middle_name'] ?? '') . ' ' . ($feedback['last_name'] ?? '')) . '</p>';
                        echo '<p class="mb-1"><strong>Stars:</strong> ' . str_repeat('⭐', $feedback['stars']) . '</p>';
                        echo '<p class="mb-0">' . htmlspecialchars($feedback['review']) . '</p>';
                        echo '</div>';
                        echo '</div>';
                        $isActive = false;
                    }
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
                ?>
            </div>
            <div class="mt-3">
                <a href="feedback" class="btn btn-primary btn-lg">See All Feedback</a>
            </div>
        </div>
    </div>


    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <h5>Donation Management System</h5>
            <p>A platform to connect donors with recipients in need.</p>
            <div class="mt-3">
                <a href="index.php" class="text-white mx-2">Home</a>
                <a href="donations.php" class="text-white mx-2">Donations</a>
                <a href="about.php" class="text-white mx-2">About Us</a>
            </div>
            <div class="mt-3">
                <p>info@donationsystem.com<br>+123 456 7890</p>
            </div>
            <p class="mt-3 mb-0">&copy; 2025 Donation Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</>

</html>