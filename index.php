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

<body>
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
        <h2>Feedback</h2>
        <div id="feedback-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                // Example feedbacks array
                $feedbacks = [
                    "This platform has changed lives. Highly recommended!",
                    "A seamless way to donate and make a difference.",
                    "Transparent and trustworthy donation system.",
                    "Thank you for making it so easy to help others."
                ];

                foreach ($feedbacks as $index => $feedback) {
                    $activeClass = $index === 0 ? 'active' : '';
                    echo "<div class='carousel-item $activeClass'>";
                    echo "<p class='lead'>$feedback</p>";
                    echo "</div>";
                }
                ?>
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
</body>

</html>