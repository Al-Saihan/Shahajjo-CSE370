<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SESSION['role'] !== 'donor') {
    header("Location: ../unauthorized.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $stars = (int)($_POST['stars'] ?? 0);
    $feedback = htmlspecialchars($_POST['feedback'] ?? '');
    
    // Validate inputs
    if (!$stars || empty($feedback)) {
        $_SESSION['feedback_error'] = "All fields are required.";
        header("Location: profile.php");
        exit();
    }

    if ($stars < 1 || $stars > 5) {
        $_SESSION['feedback_error'] = "Please select a rating between 1 and 5 stars.";
        header("Location: profile.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO feedback_table (uid, review, posting_date, stars)
            VALUES (?, ?, CURDATE(), ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $feedback, $stars]);
        $_SESSION['feedback_success'] = "Thank you for your feedback!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            try {
                $stmt = $pdo->prepare("
                    UPDATE feedback_table 
                    SET review = ?, stars = ?, posting_date = CURDATE()
                    WHERE uid = ?
                ");
                $stmt->execute([$feedback, $stars, $_SESSION['user_id']]);
                $_SESSION['feedback_success'] = "Your feedback was updated!";
            } catch (PDOException $updateError) {
                $_SESSION['feedback_error'] = "Update failed: " . $updateError->getMessage();
            }
        } else {
            $_SESSION['feedback_error'] = "Submission failed: " . $e->getMessage();
        }
    }

    header("Location: profile.php");
    exit();
}
?>
