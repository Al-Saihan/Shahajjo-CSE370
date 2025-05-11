<?php
require_once __DIR__ . '/../includes/config.php';

try {
    // Total Donations Metrics
    $totalDonations = $pdo->query("SELECT COUNT(*) FROM total_donations")->fetchColumn();
    $processedDonations = $pdo->query("SELECT COUNT(*) FROM total_donations WHERE confirmation = 1")->fetchColumn();
    $pendingDonations = $pdo->query("SELECT COUNT(*) FROM total_donations WHERE confirmation = 0")->fetchColumn();
    $rejectedDonations = $pdo->query("SELECT COUNT(*) FROM total_donations WHERE confirmation = 2")->fetchColumn();

    // Financial Totals
    $totalAmount = $pdo->query("SELECT SUM(donations_amount) FROM total_donations")->fetchColumn();
    $processedAmount = $pdo->query("SELECT SUM(donations_amount) FROM total_donations WHERE confirmation = 1")->fetchColumn();
    $pendingAmount = $pdo->query("SELECT SUM(donations_amount) FROM total_donations WHERE confirmation = 0")->fetchColumn();
    $rejectedAmount = $pdo->query("SELECT SUM(donations_amount) FROM total_donations WHERE confirmation = 2")->fetchColumn();

    // Donation Type Breakdown
    $financialDonations = $pdo->query("SELECT COUNT(*) FROM financial_donations")->fetchColumn();
    $jakatDonations = $pdo->query("SELECT COUNT(*) FROM jakat_donation")->fetchColumn();

    // Recipient and Donor Stats
    $recipientsHelped = $pdo->query("SELECT COUNT(DISTINCT recipient_id) FROM total_donations WHERE confirmation = 1")->fetchColumn();
    $activeDonors = $pdo->query("SELECT COUNT(DISTINCT donor_id) FROM total_donations")->fetchColumn();

    // Recent Donations (last 5)
    $recentDonations = $pdo->query("
        SELECT td.donation_no, td.donations_amount, td.donation_date, td.confirmation,
               CONCAT(ru.first_name, ' ', COALESCE(ru.middle_name, ''), ' ', ru.last_name) AS recipient_name
        FROM total_donations td
        JOIN donor_table d ON td.donor_id = d.id
        JOIN user_table du ON d.user_id = du.id
        JOIN recipient_table r ON td.recipient_id = r.id
        JOIN user_table ru ON r.user_id = ru.id
        ORDER BY td.donation_date DESC
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Donation Report | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }

        .report-table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            background-color: rgb(44, 7, 87);
            color: white;
        }

        body {
            background: linear-gradient(to right, rgb(104, 91, 153), rgb(204, 205, 206));
        }

        .bg-foot {
            background-color: rgb(44, 7, 87);
        }

        .btn-bt {
            background-color: rgb(95, 83, 109);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .status-processed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(60, 50, 65, 0.6);">
        <div class="container">
            <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white text-center">Donation Dashboard</h2>
                <p class="text-white text-center">Comprehensive overview of all donation activities</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card p-4 text-center">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="stat-value"><?= $totalDonations ?></div>
                    <div class="stat-label">Total Donations</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card p-4 text-center">
                    <div class="stat-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?= $processedDonations ?></div>
                    <div class="stat-label">Successful Donations</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card p-4 text-center">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?= $pendingDonations ?></div>
                    <div class="stat-label">Pending Donations</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card p-4 text-center">
                    <div class="stat-icon text-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value"><?= $rejectedDonations ?></div>
                    <div class="stat-label">Rejected Donations</div>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="stat-card p-4">
                    <h5 class="text-center mb-4">Financial Overview</h5>
                    <div class="row">
                        <div class="col-6 text-center mb-3">
                            <div class="stat-value">৳<?= $totalAmount !== null ? number_format($totalAmount, 2) : '---' ?></div>
                            <div class="stat-label">Total Donated</div>
                        </div>
                        <div class="col-6 text-center mb-3">
                            <div class="stat-value">৳<?= $processedAmount !== null ? number_format($processedAmount, 2) : '---' ?></div>
                            <div class="stat-label">Successfully Processed</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="stat-value">৳<?= $pendingAmount !== null ? number_format($pendingAmount, 2) : '---' ?></div>
                            <div class="stat-label">Pending Amount</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="stat-value">৳<?= $rejectedAmount !== null ? number_format($rejectedAmount, 2) : '---' ?></div>
                            <div class="stat-label">Rejected Amount</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="stat-card p-4">
                    <h5 class="text-center mb-4">Donation Breakdown</h5>
                    <div class="row">
                        <div class="col-6 text-center mb-3">
                            <div class="stat-value"><?= $financialDonations ?></div>
                            <div class="stat-label">Financial Donations</div>
                        </div>
                        <div class="col-6 text-center mb-3">
                            <div class="stat-value"><?= $jakatDonations ?></div>
                            <div class="stat-label">Jakat Donations</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="stat-value"><?= $recipientsHelped ?></div>
                            <div class="stat-label">Recipients Helped</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="stat-value"><?= $activeDonors ?></div>
                            <div class="stat-label">Active Donors</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Donations Table -->
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card report-table">
                    <div class="card-header table-header">
                        <h3 class="mb-0 text-center">Recent Donations</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Donation #</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentDonations)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No donation records found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentDonations as $donation): ?>
                                            <tr>
                                                <td>#<?= $donation['donation_no'] ?></td>
                                                <td>৳<?= number_format($donation['donations_amount'], 2) ?></td>
                                                <td><?= date('M j, Y', strtotime($donation['donation_date'])) ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($donation['confirmation']) {
                                                        case 0:
                                                            $statusClass = 'status-pending';
                                                            $statusText = 'Pending';
                                                            break;
                                                        case 1:
                                                            $statusClass = 'status-processed';
                                                            $statusText = 'Processed';
                                                            break;
                                                        case 2:
                                                            $statusClass = 'status-rejected';
                                                            $statusText = 'Rejected';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple animation for stat cards on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>

</html>