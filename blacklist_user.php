<?php
// blacklist_user.php
session_start();
// Check if the user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$admin_id = $_SESSION['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blacklist User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-dark bg-dark p-3">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">Admin ID: <?php echo htmlspecialchars($admin_id); ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-4">
    <h2 class="mb-4">Blacklist User</h2>

    <!-- You can build your blacklist form or table here -->
    <div class="card">
        <div class="card-body">
            <p>This is where you will display user blacklist options, such as selecting a user, confirming blacklisting, and submitting the action.</p>
            <!-- Sample form -->
            <form>
                <div class="mb-3">
                    <label for="userEmail" class="form-label">User Email</label>
                    <input type="email" class="form-control" id="userEmail" placeholder="Enter user email">
                </div>
                <button type="submit" class="btn btn-danger">Blacklist User</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
