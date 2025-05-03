<?php 
include '../includes/header.php'; 
include '../includes/config.php';
include '../includes/auth.php'; // Check if user is logged in
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id = $_SESSION['user_id'];
    $recipient_id = sanitize($_POST['recipient_id']);
    $amount = sanitize($_POST['amount']);
    $type = sanitize($_POST['type']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert into total donations
        $sql = "INSERT INTO Total_donations (Donor_ID, Recipient_ID, Donations_amount, donation_type)
                VALUES ($donor_id, $recipient_id, $amount, '$type')";
        $conn->query($sql);
        $donation_id = $conn->insert_id;
        
        // Insert into specific donation type table
        if ($type == 'financial') {
            $conn->query("INSERT INTO Financial_donations (Donation_no, Money_amount) VALUES ($donation_id, $amount)");
        } elseif ($type == 'essential') {
            $item_name = sanitize($_POST['item_name']);
            $quantity = sanitize($_POST['quantity']);
            $conn->query("INSERT INTO Essential_needs (Donation_no, Item_name, Item_quantity) VALUES ($donation_id, '$item_name', $quantity)");
        } elseif ($type == 'jakat') {
            $conn->query("INSERT INTO Jakat_donation (Donation_no, Jakat_amount) VALUES ($donation_id, $amount)");
        }
        
        // Update donor's total donation
        $conn->query("UPDATE Donor_table SET Total_donation = Total_donation + $amount, Last_donation = CURDATE() WHERE Donor_UID = $donor_id");
        
        // Update recipient's account
        $conn->query("UPDATE Recipient_account SET Money = Money + $amount WHERE Recipient_UID = $recipient_id");
        
        // Commit transaction
        $conn->commit();
        
        echo "<div class='alert alert-success'>Donation recorded successfully!</div>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<h2>Make a Donation</h2>
<form method="post" action="">
    <div class="mb-3">
        <label class="form-label">Recipient</label>
        <select class="form-select" name="recipient_id" required>
            <option value="">Select Recipient</option>
            <?php
            $result = $conn->query("SELECT u.UID, u.F_name, u.L_Name FROM User_table u 
                                  JOIN Recipient_table r ON u.UID = r.Recipient_UID
                                  WHERE u.user_type = 'recipient'");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['UID']}'>{$row['F_name']} {$row['L_Name']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Donation Type</label>
        <select class="form-select" name="type" id="donationType" required>
            <option value="">Select Type</option>
            <option value="financial">Financial Donation</option>
            <option value="essential">Essential Needs</option>
            <option value="jakat">Jakat Donation</option>
        </select>
    </div>
    <div class="mb-3" id="amountField">
        <label class="form-label">Amount</label>
        <input type="number" step="0.01" class="form-control" name="amount">
    </div>
    <div class="mb-3 d-none" id="itemField">
        <label class="form-label">Item Name</label>
        <input type="text" class="form-control" name="item_name">
        <label class="form-label mt-2">Quantity</label>
        <input type="number" class="form-control" name="quantity">
    </div>
    <button type="submit" class="btn btn-primary">Submit Donation</button>
</form>

<script>
document.getElementById('donationType').addEventListener('change', function() {
    const type = this.value;
    const amountField = document.getElementById('amountField');
    const itemField = document.getElementById('itemField');
    
    if (type === 'essential') {
        amountField.classList.add('d-none');
        itemField.classList.remove('d-none');
    } else {
        amountField.classList.remove('d-none');
        itemField.classList.add('d-none');
    }
});
</script>

<?php include '../includes/footer.php'; ?>