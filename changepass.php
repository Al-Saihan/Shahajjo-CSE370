<?php
require_once 'includes/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$errors = isset($_SESSION['changepass_error']) ? (array)$_SESSION['changepass_error'] : [];
unset($_SESSION['changepass_error']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgb(248, 249, 250);
        }

        .auth-container {
            max-width: 500px;
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 1rem;
        }

        .auth-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .form-control {
            padding: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .btn-auth {
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <div class="auth-logo"><a href="index.php" style="text-decoration: none; color: #0d6efd; transition: color 0.3s;" onmouseover="this.style.color='#0e5ed6';" onmouseout="this.style.color='#0d6efd';">Shahajjo</a></div>
                <h2 class="auth-title">Change Password</h2>
            </div>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form action="process_changepass.php" method="POST">
                <input type="email" class="form-control" name="email" placeholder="Email">
                <input type="password" class="form-control" name="newpassword" placeholder="Enter New Password">

                <button type="submit" class="btn btn-primary btn-auth">Confirm</button>
            </form>

            <div class="auth-footer">
                <p>Remembered your password? <a href="login.php">Login here</a></p>
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>