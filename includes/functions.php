<?php
// Function to display alert messages
function displayAlert()
{
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        echo "<div class='alert alert-$type alert-dismissible fade show'>";
        echo $_SESSION['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo "</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Function to get user type
function getUserType($user_id)
{
    global $conn;
    $sql = "SELECT user_type FROM User_table WHERE UID = $user_id";
    $result = $conn->query($sql);
    return $result->fetch_assoc()['user_type'];
}

// Function to get donor details
function getDonorDetails($donor_id)
{
    global $conn;
    $sql = "SELECT u.*, d.Total_donation, d.Last_donation, d.Total_income 
            FROM User_table u 
            JOIN Donor_table d ON u.UID = d.Donor_UID 
            WHERE u.UID = $donor_id";
    return $conn->query($sql)->fetch_assoc();
}

// Function to get recipient details
function getRecipientDetails($recipient_id)
{
    global $conn;
    $sql = "SELECT u.*, r.Recipient_income, r.Last_received, a.Account_no, a.Money
            FROM User_table u 
            JOIN Recipient_table r ON u.UID = r.Recipient_UID
            JOIN Recipient_account a ON u.UID = a.Recipient_UID
            WHERE u.UID = $recipient_id";
    return $conn->query($sql)->fetch_assoc();
}
