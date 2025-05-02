<?php
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/donation_system/login.php");
    exit();
}

// Database connection
require_once __DIR__ . '/../../includes/config.php';
?>

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM User_table WHERE UID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recipient Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                <?php echo htmlspecialchars($_SESSION['f_name']) . ' ' . htmlspecialchars($_SESSION['l_name']); ?>

                </span>
                <a href="../logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Recipient Profile</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['F_name'] . ' ' . htmlspecialchars($user['L_Name'])); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>