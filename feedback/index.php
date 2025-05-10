<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SESSION['role'] !== 'recipient') {
    header("Location: ../unauthorized.php");
    exit();
}

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
        ORDER BY 
            feedback_table.posting_date DESC");
    $stmt->execute();
    $feedbacks = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Feedbacks | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .rating-stars {
            color: gold;
            font-size: 1.2rem;
        }
        .feedback-table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-header {
            background-color: rgb(44, 7, 87);
            color: white;
        }
        body {
            background: linear-gradient(to right, rgb(104, 91, 153), rgb(204, 205, 206));
        }
        .bg-foot {
            background-color:rgb(44, 7, 87);
        }
        .btn-bt {
            background-color:rgb(95, 83, 109); 
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(60, 50, 65, 0.6);">
        <div class="container">
            <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="profile.php">Back to Profile</a>
                <a class="nav-link" href="../process_logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card feedback-table">
                    <div class="card-header table-header">
                        <h3 class="mb-0 text-center">All Feedback Records</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reviewer</th>
                                        <th>Rating</th>
                                        <th>Feedback</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($feedbacks)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No feedback records found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($feedbacks as $feedback): ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars(
                                                        $feedback['first_name'] . ' ' . 
                                                        ($feedback['middle_name'] ? $feedback['middle_name'] . ' ' : '') . 
                                                        ($feedback['last_name'] ?? '')
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <span class="rating-stars">
                                                        <?= str_repeat('★', $feedback['stars']) ?>
                                                        <?= str_repeat('☆', 5 - $feedback['stars']) ?>
                                                    </span>
                                                    (<?= $feedback['stars'] ?>/5)
                                                </td>
                                                <td><?= htmlspecialchars($feedback['review']) ?></td>
                                                <td><?= date('M j, Y', strtotime($feedback['posting_date'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-foot">
                        <a href="../index.php" class="btn btn-bt text-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>