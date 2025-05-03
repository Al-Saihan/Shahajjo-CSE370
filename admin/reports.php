<?php 
include '../../includes/header.php';
requireAdmin();
?>

<div class="row">
    <div class="col-md-12">
        <h2>Financial Reports</h2>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm rounded-lg">
            <div class="card-body">
                <h5 class="card-title">Donation Summary</h5>
                <canvas id="donationChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm rounded-lg">
            <div class="card-body">
                <h5 class="card-title">Recent Transactions</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Donor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT d.Donation_date, d.donation_type, d.Donations_amount, u.F_name, u.L_Name 
                                    FROM Total_donations d
                                    JOIN User_table u ON d.Donor_ID = u.UID
                                    ORDER BY d.Donation_date DESC LIMIT 5";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<tr>
                                            <td>'.date('M d', strtotime($row['Donation_date'])).'</td>
                                            <td>'.ucfirst($row['donation_type']).'</td>
                                            <td>$'.number_format($row['Donations_amount'], 2).'</td>
                                            <td>'.$row['F_name'].' '.$row['L_Name'].'</td>
                                          </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js implementation
const ctx = document.getElementById('donationChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Financial', 'Essential', 'Jakat'],
        datasets: [{
            label: 'Donation Types',
            data: [
                <?php
                $sql = "SELECT SUM(Donations_amount) as total FROM Total_donations WHERE donation_type = 'financial'";
                echo $conn->query($sql)->fetch_assoc()['total'] ?? 0;
                ?>, 
                <?php
                $sql = "SELECT SUM(Donations_amount) as total FROM Total_donations WHERE donation_type = 'essential'";
                echo $conn->query($sql)->fetch_assoc()['total'] ?? 0;
                ?>, 
                <?php
                $sql = "SELECT SUM(Donations_amount) as total FROM Total_donations WHERE donation_type = 'jakat'";
                echo $conn->query($sql)->fetch_assoc()['total'] ?? 0;
                ?>
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value;
                    }
                }
            }
        }
    }
});
</script>

<?php include '../../includes/footer.php'; ?>