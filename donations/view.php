<?php 
include '../includes/header.php';
requireLogin();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit();
}

$donation_id = sanitize($_GET['id']);
$user_id = $_SESSION['user_id'];
$user_type = getUserType($user_id);

// Base query
$sql = "SELECT d.*, 
               donor.F_name as donor_fname, donor.L_name as donor_lname, donor.email as donor_email,
               recip.F_name as recip_fname, recip.L_name as recip_lname, recip.email as recip_email
        FROM Total_donations d
        JOIN User_table donor ON d.Donor_ID = donor.UID
        JOIN User_table recip ON d.Recipient_ID = recip.UID
        WHERE d.Donation_no = $donation_id";

// Add permission check based on user type
if ($user_type == 'donor') {
    $sql .= " AND d.Donor_ID = $user_id";
} elseif ($user_type == 'recipient') {
    $sql .= " AND d.Recipient_ID = $user_id";
}

$result = $conn->query($sql);

if ($result->num_rows != 1) {
    header("Location: list.php");
    exit();
}

$donation = $result->fetch_assoc();

// Get specific donation details based on type
$details = [];
switch ($donation['donation_type']) {
    case 'financial':
        $sql = "SELECT * FROM Financial_donations WHERE Donation_no = $donation_id";
        break;
    case 'essential':
        $sql = "SELECT * FROM Essential_needs WHERE Donation_no = $donation_id";
        break;
    case 'jakat':
        $sql = "SELECT * FROM Jakat_donation WHERE Donation_no = $donation_id";
        break;
}

$details_result = $conn->query($sql);
if ($details_result->num_rows == 1) {
    $details = $details_result->fetch_assoc();
}
?>

<div class="card shadow-sm rounded-lg">
    <div class="card-body">
        <h2 class="card-title">Donation Details #<?php echo $donation['Donation_no']; ?></h2>
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <h4>Basic Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Date</th>
                        <td><?php echo date('M d, Y H:i', strtotime($donation['Donation_date'])); ?></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td><?php echo ucfirst($donation['donation_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>$<?php echo number_format($donation['Donations_amount'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php echo $donation['Confirmation'] ? 
                                 '<span class="badge bg-success">Confirmed</span>' : 
                                 '<span class="badge bg-warning">Pending</span>'; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4>Parties Involved</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Donor</th>
                        <td>
                            <?php echo $donation['donor_fname'].' '.$donation['donor_lname']; ?><br>
                            <small><?php echo $donation['donor_email']; ?></small>
                        </td>
                    </tr>
                    <tr>
                        <th>Recipient</th>
                        <td>
                            <?php echo $donation['recip_fname'].' '.$donation['recip_lname']; ?><br>
                            <small><?php echo $donation['recip_email']; ?></small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Donation Details</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <?php if($donation['donation_type'] == 'financial'): ?>
                        <tr>
                            <th>Money Amount</th>
                            <td>$<?php echo number_format($details['Money_amount'], 2); ?></td>
                        </tr>
                        <?php elseif($donation['donation_type'] == 'essential'): ?>
                        <tr>
                            <th>Item Name</th>
                            <td><?php echo $details['Item_name']; ?></td>
                        </tr>
                        <tr>
                            <th>Item Quantity</th>
                            <td><?php echo $details['Item_quantity']; ?></td>
                        </tr>
                        <?php elseif($donation['donation_type'] == 'jakat'): ?>
                        <tr>
                            <th>Jakat Amount</th>
                            <td>$<?php echo number_format($details['Jakat_amount'], 2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="list.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>