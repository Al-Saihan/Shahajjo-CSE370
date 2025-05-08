<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once 'includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = $_SESSION['register_errors'] ?? [];
$form_data = $_SESSION['register_data'] ?? [];

unset($_SESSION['register_errors']);

$registration_success = isset($_SESSION['registration_success']);
if ($registration_success) {
    unset($_SESSION['registration_success']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Admin | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .auth-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
        }

        .auth-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .role-selection {
            margin: 1.5rem 0;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-container">

            <div class="auth-header">
                <div class="auth-logo text-center">
                    <a href="admin_dashboard.php" style="text-decoration: none; font-weight: bold; color: red; font-size: 2.5rem; transition: color 0.3s;" onmouseover="this.style.color='#b30000';" onmouseout="this.style.color='red';">Shahajjo</a>
                </div>
            </div>

            <h2 class="text-center mb-4">Register A New Admin</h2>

            <form action="process_admin_register.php" method="POST" onsubmit="return validateForm()">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                            value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name"
                            value="<?= htmlspecialchars($form_data['middle_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                            value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>" required>
                    </div>
                </div>


                <div class="mb-3">
                    <label for="username" class="form-label">Admin Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        value="<?= htmlspecialchars($form_data['username'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                    <small class="text-muted">Must be at least 8 characters</small>
                </div>



                <div class="role-selection">
                    <label class="form-label">Access Level</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="access" id="super_admin" value="super_admin" required>
                        <label class="form-check-label" for="super_admin">
                            Super Admin (Can Add New Admins & Manage Users)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="access" id="moderator" value="moderator" required>
                        <label class="form-check-label" for="moderator">
                            Moderator (Can Manage Users)
                        </label>
                    </div>
                </div>

                <!-- THROW ERROR MESSAGE HERE -->
                <?php if ($registration_success): ?>
                    <div class="alert alert-success">
                        Admin Added Successfully.
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <!-- ERROR MESSAGE ENDS HERE -->


                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to add this admin?\nKindly Re-Check the information, before proceeding.')">Add Admin</button>
            </form>

            <div class="text-center mt-3">
                <p> <a href="admin_dashboard.php" style="color: red;">Go Back To Dashboard</a></p>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                alert('Password must be at least 8 characters');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>