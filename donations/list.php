<?php 
include '../includes/header.php';
requireLogin();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Donations</h2>
    <?php if(getUserType($_SESSION['user_id']) == 'donor'): ?>
    <a href="add.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Donation
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm rounded-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Donor</th>
                        <th>Recipient</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user_id = $_SESSION['user_id'];
                    $user_type = getUserType($user_id);
                    
                    if ($user_type == 'admin') {
                        $sql = "SELECT d.*, 
                                       donor.F_name as donor_fname, donor.L_name as donor_lname,
                                       recip.F_name as recip_fname, recip.L_name as recip_lname
                                FROM Total_donations d
                                JOIN User_table donor ON d.Donor_ID = donor.UID
                                JOIN User_table recip ON d.Recipient_ID = recip.UID
                                ORDER BY d.Donation_date DESC";
                    } elseif ($user_type == 'donor') {
                        $sql = "SELECT d.*, 
                                       recip.F_name as recip_fname, recip.L_name as recip_lname
                                FROM Total_donations d
                                JOIN User_table recip ON d.Recipient_ID = recip.UID
                                WHERE d.Donor_ID = $user_id
                                ORDER BY d.Donation_date DESC";
                    } elseif ($user_type == 'recipient') {
                        $sql = "SELECT d.*, 
                                       donor.F_name as donor_fname, donor.L_name as donor_lname
                                FROM Total_donations d
                                JOIN User_table donor ON d.Donor_ID = donor.UID
                                WHERE d.Recipient_ID = $user_id
                                ORDER BY d.Donation_date DESC";
                    }
                    
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo '<tr>
                                    <td>'.$row['Donation_no'].'</td>
                                    <td>'.date('M d, Y', strtotime($row['Donation_date'])).'</td>
                                    <td>';
                            if ($user_type != 'donor') {
                                echo $row['donor_fname'].' '.$row['donor_lname'];
                            } else {
                                echo 'You';
                            }
                            echo '</td>
                                  <td>';
                            if ($user_type != 'recipient') {
                                echo $row['recip_fname'].' '.$row['recip_lname'];
                            } else {
                                echo 'You';
                            }
                            echo '</td>
                                  <td>'.ucfirst($row['donation_type']).'</td>
                                  <td>$'.number_format($row['Donations_amount'], 2).'</td>
                                  <td>';
                            echo $row['Confirmation'] ? 
                                 '<span class="badge bg-success">Confirmed</span>' : 
                                 '<span class="badge bg-warning">Pending</span>';
                            echo '</td>
                                  <td>
                                    <a href="view.php?id='.$row['Donation_no'].'" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>';
                            if ($user_type == 'admin' && !$row['Confirmation']) {
                                echo '<a href="process.php?confirm='.$row['Donation_no'].'" class="btn btn-sm btn-success ms-1">
                                        <i class="fas fa-check"></i>
                                      </a>';
                            }
                            echo '</td>
                                  </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="8">No donations found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>