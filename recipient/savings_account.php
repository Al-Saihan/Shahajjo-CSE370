<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/access_check.php';

if ($_SESSION['role'] !== 'recipient') {
    header("Location: ../unauthorized.php");
    exit();
}

// Get recipient and savings account data
try {
    // Get recipient basic info
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, r.id, r.wallet
        FROM user_table u
        JOIN recipient_table r ON u.id = r.user_id
        WHERE u.id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recipient = $stmt->fetch();
    
    if (!$recipient) {
        die("Recipient not found");
    }
    
    // Get savings account info
    $stmt = $pdo->prepare("
        SELECT * FROM savings_account 
        WHERE recipient_uid = ?
    ");
    $stmt->execute([$recipient['id']]);
    $account = $stmt->fetch();
    
    if (!$account) {
        $_SESSION['account_message'] = "You don't have a savings account yet. Please create one first.";
        header("Location: profile.php");
        exit();
    }
    
    // Calculate days since account creation
    $createdDate = new DateTime($account['created_at']);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($createdDate);
    $daysActive = $interval->days;
    
    // Update time_limit in database
    $stmt = $pdo->prepare("
        UPDATE savings_account 
        SET time_limit = ?
        WHERE account_no = ?
    ");
    $stmt->execute([$daysActive, $account['account_no']]);
    
    // Refresh account data
    $stmt = $pdo->prepare("
        SELECT * FROM savings_account 
        WHERE recipient_uid = ?
    ");
    $stmt->execute([$recipient['id']]);
    $account = $stmt->fetch();
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Savings Account | Shahajjo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .account-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.24);
        }
        .bg-dark_pink {
            background-color: rgba(61, 17, 55, 0.58);
        }
        .bg-dp {
            background-color: rgba(61, 17, 55, 0.35);
        }
        .btn-pp {
            background-color: rgba(61, 17, 55, 0.35);
        }
        
        /* ADD YOUR TABLE STYLES HERE */
        .wd-table {
            --bs-table-bg: transparent;
            --bs-table-color: #fff;
            --bs-table-border-color: rgba(255, 255, 255, 0.1);
        }
        
        .wd-table thead {
            background-color: rgba(39, 2, 34, 0.85);
            color: #fff;
        }
        
        .wd-table tbody {
            background-color: rgba(230, 214, 229, 0.47);
        }
        
        .wd-table tbody tr:hover {
            background-color: rgba(99, 3, 32, 0.25) !important;
        }
        
    </style>
</head>
<body style="background: linear-gradient(to right, rgb(92, 74, 91), rgb(204, 205, 206));">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(60, 50, 65, 0.6);">
    <div class="container"> 
        <a class="navbar-brand fs-2 fw-bold" href="../index.php">Shahajjo</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">
                Recipient ID: <?= $recipient['id'] ?>
            </span>
            <a class="nav-link" href="../process_logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">          
            <div class="card account-card mb-4">
                <div class="card-header bg-dark_pink text-light">
                    <h4>Savings Account Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Account Holder</h5>
                            <style>
                                .text-dp {
                                    color: rgb(88, 11, 53);
                                    font-weight: bold;
                                }
                            </style>
                            <p class="fs-2 fw-semibold text-dp"><?= htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Account Number</h5>
                            <p><?= $account['account_no'] ?></p>
                        </div>
                    </div>
                    <style>
                        .text-taka {
                            color: rgb(176, 235, 175); 
                            font-weight: bold;
                        }
                    </style>
                    <div class="row mb-4 bg-dp p-3 rounded">
                        <div class="col-md-6">
                            <h5>Current Balance</h5>
                            <style>
                                .btn-depo {
                                    background-color:rgb(61, 17, 55); 
                                }
                            </style>
                            <style>
                                .text-depo {
                                    color: rgba(204, 205, 206);
                                    font-weight: bold;
                                }
                            </style>
                            <p class="fw-bold fs-1 text-taka">৳<?= number_format($account['money'], 2) ?></p>
                            
                        </div>
                        <div class="col-md-6">
                            <h5>Account Created On</h5>
                            <p><?= date('F j, Y', strtotime($account['created_at'])) ?></p>
                            
                            <hr>
                            <!-- Deposit Button -->
                            <button type="button" class="btn btn-depo text-depo mt-3" data-bs-toggle="modal" data-bs-target="#depositModal">Deposit</button>
                            
                            <!-- Withdraw Button -->
                            <button type="button" class="btn btn-depo text-depo mt-3" data-bs-toggle="modal" data-bs-target="#withdrawModal">Withdraw</button>

                        </div>                     
                    </div>

                    <!-- THROW ERROR MESSAGE HERE -->
                    <?php if (isset($_SESSION['deposit_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['deposit_success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['deposit_success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['deposit_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['deposit_error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['deposit_error']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['withdraw_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['withdraw_success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['withdraw_success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['withdraw_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($_SESSION['withdraw_error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['withdraw_error']); ?>
                    <?php endif; ?>
                    <!-- ERROR MESSAGE ENDS HERE -->

                    
                    <div class="row mb-4 bg-dp p-3 rounded">
                        <div class="col-md-12 fw-bold">
                            <h5>Account Activity</h5>
                            <p>Your account has been active for <?= $account['time_limit'] ?> days</p>
                        </div>
                    </div>

                    <!-- Withdrawal log -->
                    <div class="row mb-4 bg-dp p-3 rounded">
                        <h3>Withdrawal Log</h3>
                        <?php
                        $stmt = $pdo->prepare("SELECT w.withdrawal_id, w.account_no, w.amount, w.withdrawal_method, w.transaction_date 
                            FROM savings_withdrawal_log w
                            JOIN savings_account s ON w.account_no = s.account_no
                            WHERE s.recipient_uid = ?
                            ORDER BY w.transaction_date DESC");
                        $stmt->execute([$recipient['id']]);

                        $withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($withdrawals)): ?>
                            <p>No withdrawal history found.</p>
                        <?php else: ?>

                            <div class="table-responsive">
                                        <table class="table wd-table table-borderless table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="ps-3">Date</th>
                                                    <th class="text-end pe-3">Amount</th>
                                                    <th class="text-center">Method</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($withdrawals as $withdrawal): ?>
                                                <tr>
                                                    <td class="ps-3 text-dark fw-bold"><?= date('M j, Y g:i A', strtotime($withdrawal['transaction_date'])) ?></td>
                                                    <td class="text-end pe-3 text-dark fw-bold">৳<?= number_format($withdrawal['amount'], 2) ?></td>
                                                    <td class="text-center text-capitalize text-dark fw-bold"><?= $withdrawal['withdrawal_method'] ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                    <style>
                        .bg-sakib {
                            background-color:rgba(60, 50, 65, 0.6);
                        }
                    </style>
                    <div class="text-center mt-4">
                        <a href="profile.php" class="btn btn-pp fw-bold">Back to Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Deposit popup(modal) and everything in it  -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.85);">
            <div class="modal-header bg-dark_pink text-light">
                <h5 class="modal-title" id="depositModalLabel">Deposit to Savings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="depositForm" method="POST" action="process_deposit.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="depositAmount" class="form-label">Amount (৳)</label>
                        <input type="number" class="form-control" id="depositAmount" name="amount" min="1" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentMethod" name="payment_method" required>
                            <option value="" selected disabled>Select payment method</option>
                            <option value="Bkash">Bkash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Upay">Upay</option>
                            <option value="Bank_Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <input type="hidden" name="account_no" value="<?= $account['account_no'] ?>">
                </div>

                <!-- final deposit & close button -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-pp">Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdraw popup(modal) and everything in it  -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.85);">
            <div class="modal-header bg-dark_pink text-light">
                <h5 class="modal-title" id="withdrawModalLabel">Withdraw from Savings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="process_withdraw.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="withdrawAmount" class="form-label">Amount (৳)</label>
                        <input type="number" class="form-control" id="withdrawAmount" name="amount" min="1" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="withdrawMethod" class="form-label">Withdrawal Method</label>
                        <select class="form-select" id="withdrawMethod" name="payment_method" required>
                            <option value="" selected disabled>Select withdrawal method</option>
                            <option value="Bkash">Bkash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Upay">Upay</option>
                            <option value="Bank_Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <input type="hidden" name="account_no" value="<?= $account['account_no'] ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-pp">Withdraw</button>
                </div>
            </form>
        </div>
    </div>
</div>


</body>
</html>