<?php
require_once '../includes/config.php';
session_start();

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../unauthorized.php");
    exit();
}

$admin_id = null;
try {
    $stmt = $pdo->prepare("SELECT admin_id FROM user_table WHERE id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch();
    if ($result) {
        $admin_id = $result['admin_id'];
    } else {
        die("Admin ID not found for the current user.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Get all users with complete details
try {
    // GET METHOD to get the sorting parameters
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';  // Default to ID
    $sort_order = isset($_GET['sort_order']) ? strtoupper($_GET['sort_order']) : 'ASC';  // Default to ASC

    $stmt = $pdo->query("
        SELECT 
            u.id, u.first_name, u.middle_name, u.last_name, u.email,
            u.role, u.created_at, u.status, u.admin_id,
            d.address AS donor_address, d.contact_number AS donor_contact,
            r.address AS recipient_address, r.contact_number AS recipient_contact
        FROM user_table u
        LEFT JOIN donor_table d ON u.id = d.user_id
        LEFT JOIN recipient_table r ON u.id = r.user_id
        ORDER BY $sort_by $sort_order;
    ");
    // ORDER BY u.role DESC, u.id ASC;
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error 1: " . $e->getMessage());
}

// Get admin details for the currently logged-in admin
try {
    $stmt = $pdo->prepare("
        SELECT 
        *
        FROM admin_table
        WHERE admin_id = :admin_id
    ");
    $stmt->execute(['admin_id' => $admin_id]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    die("Database error 2: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard | Shahajjo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            max-height: 75vh;
            /* 75% of viewport height */
            overflow-y: auto;
            /* Enable vertical scrolling if needed */
        }

        .table-responsive {
            min-height: 400px;
            /* Minimum height for the table */
        }

        .user-details {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .badge-admin {
            background-color: #000000;
            color: white;
            font-weight: bold;
        }

        .badge-donor {
            background-color: #2848a7;
        }

        .badge-recipient {
            background-color: #f878d4;
        }

        .badge-unverified {
            background-color: #dc3545;
        }

        .badge-verified {
            background-color: #28a745;
        }

        .badge-blacklisted {
            background-color: #202020;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .detail-row {
            background-color: #f8f9fa;
        }

        .address-box {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .dropdown-menu.bg-dark {
            border: 1px solid #444;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
            text-align: center;
            min-width: 8rem
        }

        .dropdown-item.text-white:hover {
            background-color: #333 !important;
            color: white !important;
        }

        .btn.border-dark:hover {
            border-color: #666 !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand auth-logo fw-bold text-white fs-3" href="../index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Admin ID: <?= $_SESSION['user_id'] ?>
                </span>
                <a class="nav-link" href="../process_logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">

        <h2 class="mb-4 d-flex justify-content-between align-items-center">
            User Management
        </h2>

        <!-- SORTING HERE -->

        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="GET" action="dashboard.php" class="d-flex align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <label for="sort_by" class="form-label mb-0 me-2">Sort By:</label>
                    <select name="sort_by" id="sort_by" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="id" <?= !isset($_GET['sort_by']) || $_GET['sort_by'] === 'id' ? 'selected' : '' ?>>ID</option>
                        <option value="first_name" <?= isset($_GET['sort_by']) && $_GET['sort_by'] === 'first_name' ? 'selected' : '' ?>>Name</option>
                        <option value="status" <?= isset($_GET['sort_by']) && $_GET['sort_by'] === 'status' ? 'selected' : '' ?>>Status</option>
                        <option value="role" <?= isset($_GET['sort_by']) && $_GET['sort_by'] === 'role' ? 'selected' : '' ?>>Role</option>
                        <option value="created_at" <?= isset($_GET['sort_by']) && $_GET['sort_by'] === 'created_at' ? 'selected' : '' ?>>Joined Date</option>
                    </select>
                </div>

                <div class="d-flex align-items-center">
                    <label for="sort_order" class="form-label mb-0 me-2">Order:</label>
                    <select name="sort_order" id="sort_order" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="asc" <?= !isset($_GET['sort_order']) || strtolower($_GET['sort_order']) === 'asc' ? 'selected' : '' ?>>Ascending</option>
                        <option value="desc" <?= isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'desc' ? 'selected' : '' ?>>Descending</option>
                    </select>
                </div>
            </form>
            <a href="add_admin.php" class="btn btn-danger btn-sm">Add Admin</a>
        </div>

        <!-- SORTING END -->


        <div class="table-container" style="border: 2px solid gray;"> <!-- Added table-container class -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-center">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="border: 1px solid #dee2e6;">ID</th>
                            <th style="border: 1px solid #dee2e6;">Name</th>
                            <th style="border: 1px solid #dee2e6;">Email</th>
                            <th style="border: 1px solid #dee2e6;">Status</th>
                            <th style="border: 1px solid #dee2e6;">Role</th>
                            <th style="border: 1px solid #dee2e6;">Joined</th>
                            <th style="border: 1px solid #dee2e6;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr data-toggle="collapse" data-target="#details-<?= $user['id'] ?>" aria-expanded="false" aria-controls="details-<?= $user['id'] ?>">
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    <?php if ($user['middle_name']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($user['middle_name']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <!-- <?= htmlspecialchars($user['status']) ?> -->
                                    <span class="badge <?=
                                                        $user['status'] === 'unverified' ? 'badge-unverified' : ($user['status'] === 'verified' ? 'badge-verified' : 'badge-blacklisted')
                                                        ?>">
                                        <?= ucfirst($user['status']) ?>
                                </td>
                                <td>
                                    <span class="badge <?=
                                                        $user['role'] === 'admin' ? 'badge-admin' : ($user['role'] === 'donor' ? 'badge-donor' : 'badge-recipient')
                                                        ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                    <br>
                                    <span>
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <?php
                                            // Fetch admin_name for the admin user
                                            $admin_name = '';
                                            try {
                                                $stmt = $pdo->prepare("
                                                SELECT admin_table.admin_name 
                                                FROM user_table 
                                                INNER JOIN admin_table 
                                                ON user_table.admin_id = admin_table.admin_id 
                                                WHERE user_table.id = :user_id
                                                ");
                                                $stmt->execute(['user_id' => $user['id']]);
                                                $result = $stmt->fetch();
                                                if ($result) {
                                                    $admin_name = $result['admin_name'];
                                                }
                                            } catch (PDOException $e) {
                                                $admin_name = 'Error fetching name';
                                            }
                                            ?>
                                            <span class="badge badge-admin"><?= htmlspecialchars($admin_name) ?></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div>
                                        <!-- MANAGE BUTTON -->
                                        <button class="btn btn-sm btn-warning border border-dark" type="button" id="manageDropdown<?= $user['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu bg-dark text-white" aria-labelledby="manageDropdown<?= $user['id'] ?>">
                                            <!-- CHANGE STATUS/VERIFICATION -->
                                            <li>
                                                <form action="process_verify_user.php" method="POST" style="margin: 0;">
                                                    <?php if ($user['id'] === $admin_id): ?>
                                                        <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">You cannot change your Status</div>
                                                    <?php elseif ($user['role'] === 'admin'): ?>
                                                        <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">Admin cannot be Unverified</div>
                                                    <?php else: ?>
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="dropdown-item text-white bg-dark border-bottom border-secondary">Verify User</button>
                                                    <?php endif; ?>
                                                </form>
                                            </li>

                                            <!-- CHANGE BLACKLIST SECTION -->
                                            <li>
                                                <form action="process_blacklist_user.php" method="POST" style="margin: 0;">
                                                    <?php if ($user['id'] === $admin_id): ?>
                                                        <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">You cannot Blacklist yourself</div>
                                                    <?php elseif ($user['role'] === 'admin'): ?>
                                                        <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">Admin cannot be Blacklisted</div>
                                                    <?php else: ?>
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="dropdown-item text-white bg-dark border-bottom border-secondary">Blacklist User</button>
                                                    <?php endif; ?>
                                                </form>
                                            </li>

                                            <!-- CHANGE ACCESS LEVEL SECTION -->
                                            <?php if ($user['admin_id'] !== null): ?>
                                                <li>
                                                    <form action="process_access_level.php" method="POST" style="margin: 0;">
                                                        <?php if ($user['id'] === $admin_id): ?>
                                                            <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">You cannot change your own Access Level</div>
                                                            <!-- CHECK IF CURRENT ADMIN IS SUPER ADMIN -->
                                                        <?php elseif ($admin['access_level'] !== 'super_admin'): ?>
                                                            <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">Only Super Admin can change Access Level</div>
                                                        <?php else: ?>
                                                            <input type="hidden" name="user_id" value="<?= $user['admin_id'] ?>">
                                                            <button type="submit" class="dropdown-item text-white bg-dark border-bottom border-secondary">Change Access Level</button>
                                                        <?php endif; ?>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr class="detail-row">
                                <td colspan="7" class="p-0">
                                    <div id="details-<?= $user['id'] ?>" class="collapse">
                                        <div class="user-details p-3 text-center">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>Basic Information</h5>
                                                    <div class="address-box">

                                                        <p><strong>Full Name:</strong>
                                                            <?= htmlspecialchars($user['first_name'] . ' ' .
                                                                ($user['middle_name'] ? $user['middle_name'] . ' ' : '') .
                                                                $user['last_name']) ?>
                                                        </p>
                                                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                                        <?php if ($user['role'] !== 'admin'): ?>
                                                            <p><strong>Account Type:</strong> <?= ucfirst($user['role']) ?></p>
                                                        <?php endif; ?>

                                                        <p><strong>Registration Date:</strong> <?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">

                                                    <?php if ($user['role'] === 'donor'): ?>
                                                        <h5>Contact Information</h5>

                                                        <div class="address-box">
                                                            <p><strong>Address:</strong></p>
                                                            <?= $user['donor_address'] ? nl2br(htmlspecialchars($user['donor_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                                        </div>
                                                        <div class="address-box">
                                                            <p><strong>Phone Number:</strong></p>
                                                            <?= $user['donor_contact'] ? htmlspecialchars($user['donor_contact']) : '<span>Not provided</span>' ?></p>
                                                        </div>

                                                    <?php elseif ($user['role'] === 'recipient'): ?>
                                                        <h5>Contact Information</h5>

                                                        <div class="address-box">
                                                            <p><strong>Address:</strong></p>
                                                            <?= $user['recipient_address'] ? nl2br(htmlspecialchars($user['recipient_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                                        </div>
                                                        <div class="address-box">
                                                            <p><strong>Phone Number:</strong></p>
                                                            <?= $user['recipient_contact'] ? htmlspecialchars($user['recipient_contact']) : '<span>Not provided</span>' ?></p>
                                                        </div>

                                                    <?php else: ?>
                                                        <h5>Administration Information</h5>

                                                        <div class="address-box">
                                                            <p><strong>Account Type:</strong> <?= ucfirst($user['role']) ?></p>
                                                            <?php
                                                            try {
                                                                $stmt = $pdo->prepare("
                                                                SELECT admin_table.admin_id, admin_table.access_level
                                                                FROM user_table
                                                                INNER JOIN admin_table
                                                                ON user_table.admin_id = admin_table.admin_id
                                                                WHERE user_table.id = :user_id
                                                                ");
                                                                $stmt->execute(['user_id' => $user['id']]);
                                                                $adminDetails = $stmt->fetch();
                                                                if ($adminDetails) {
                                                                    echo '<p><strong>Admin ID:</strong> ' . htmlspecialchars($adminDetails['admin_id']) . '</p>';
                                                                    echo '<p><strong>Access Level:</strong> ' . htmlspecialchars(ucwords(str_replace('_', ' ', $adminDetails['access_level']))) . '</p>';
                                                                } else {
                                                                    echo '<p class="text-muted">Admin details not found</p>';
                                                                }
                                                            } catch (PDOException $e) {
                                                                echo '<p class="text-danger">Error fetching admin details</p>';
                                                            }
                                                            ?>
                                                        </div>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container my-5">

            <!-- DONATION MANAGEMENT -->
            <?php
            // Query to fetch donation details with user info
            try {
                // SQL query to fetch detailed donation info
                $stmt = $pdo->query("
                    SELECT 
                        td.donation_no,
                        td.donations_amount,
                        td.donation_date,
                        td.confirmation,
                        d.user_id AS donor_uid,
                        r.user_id AS recipient_uid,
                        r.cause,
                        CONCAT(du.first_name, ' ', COALESCE(du.middle_name, ''), ' ', du.last_name) AS donor_name,
                        CONCAT(ru.first_name, ' ', COALESCE(ru.middle_name, ''), ' ', ru.last_name) AS recipient_name,
                        du.email AS donor_email,
                        ru.email AS recipient_email,
                        d.address AS donor_address,
                        r.address AS recipient_address,
                        d.contact_number AS donor_contact,
                        r.contact_number AS recipient_contact
                    FROM total_donations td
                    JOIN donor_table d ON td.donor_id = d.id
                    JOIN recipient_table r ON td.recipient_id = r.id
                    JOIN user_table du ON d.user_id = du.id
                    JOIN user_table ru ON r.user_id = ru.id
                    WHERE td.confirmation = 0;
                ");

                $result = $stmt->fetchAll();
            } catch (Exception $e) {
                $_SESSION['error'] = "Error loading donation data.";
                header("Location: dashboard.php"); // or any relevant fallback
                exit();
            }
            ?>

            <div class="container mt-5">
                <h2 class="mb-4">Donation Management</h2>
                <div class="table-container" style="border: 2px solid gray;"> <!-- Added table-container class -->
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Donation No</th>
                                <th>Donor Name</th>
                                <th>Recipient Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $donation): ?>
                                <tr data-toggle="collapse" data-target="#donation-details-<?= $donation['donation_no'] ?>" aria-expanded="false">
                                    <td><?= $donation['donation_no'] ?></td>
                                    <td><?= htmlspecialchars($donation['donor_name']) ?></td>
                                    <td><?= htmlspecialchars($donation['recipient_name']) ?></td>
                                    <td>৳<?= number_format($donation['donations_amount'], 2) ?></td>
                                    <td><?= date('M j, Y', strtotime($donation['donation_date'])) ?></td>
                                    <td>
                                        <span class="badge <?= $donation['confirmation'] ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $donation['confirmation'] ? 'Confirmed' : 'Pending' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!$donation['confirmation']): ?>
                                            <form method="POST" action="process_handling_donations.php" class="d-inline">
                                                <input type="hidden" name="donation_no" value="<?= $donation['donation_no'] ?>">
                                                <button type="submit" name="action" value="confirm" class="btn btn-success btn-sm">Confirm</button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr class="detail-row">
                                    <td colspan="7" class="p-0">
                                        <div id="donation-details-<?= $donation['donation_no'] ?>" class="collapse">
                                            <div class="donation-details p-3 text-center">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5>Donor Information</h5>
                                                        <div class="address-box text-center">
                                                            <p><strong>Full Name:</strong> <?= htmlspecialchars($donation['donor_name']) ?></p>
                                                            <p><strong>Email:</strong> <?= htmlspecialchars($donation['donor_email']) ?></p>
                                                            <p><strong>Contact:</strong>
                                                                <?= $donation['donor_contact'] ? htmlspecialchars($donation['donor_contact']) : '<span class="text-muted">Not provided</span>' ?>
                                                            </p>
                                                            <p><strong>Address:</strong></p>
                                                            <?= $donation['donor_address'] ? nl2br(htmlspecialchars($donation['donor_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5>Recipient Information</h5>
                                                        <div class="address-box text-center">
                                                            <p><strong>Full Name:</strong> <?= htmlspecialchars($donation['recipient_name']) ?></p>
                                                            <p><strong>Email:</strong> <?= htmlspecialchars($donation['recipient_email']) ?></p>
                                                            <p><strong>Contact:</strong>
                                                                <?= $donation['recipient_contact'] ? htmlspecialchars($donation['recipient_contact']) : '<span class="text-muted">Not provided</span>' ?>
                                                            </p>
                                                            <p><strong>Address:</strong></p>
                                                            <?= $donation['recipient_address'] ? nl2br(htmlspecialchars($donation['recipient_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <h5>Recipient's Reason For Asking Donation:</h5>
                                                        <div class="address-box text-center">
                                                            <?= $donation['cause'] ? nl2br(htmlspecialchars($donation['cause'])) : '<p class="text-muted">No cause description provided</p>' ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if ($donation['confirmation']): ?>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="alert alert-success text-center">
                                                                <i class="fas fa-check-circle"></i> This donation was confirmed on <?= date('F j, Y \a\t g:i a', strtotime($donation['donation_date'])) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <style>
                    .detail-row {
                        background-color: #f8f9fa;
                    }

                    .donation-details {
                        background-color: #f8f9fa;
                        border-top: 1px solid #dee2e6;
                    }

                    .address-box {
                        background-color: white;
                        padding: 15px;
                        border-radius: 5px;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    }
                </style>




                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                <!-- // fixing the toggle issue with dropdowns -->

                <script>
                    $(document).ready(function() {
                        // Toggle detail rows
                        $('tr[data-toggle="collapse"]').click(function() {
                            $(this).next('tr').find('.collapse').collapse('toggle');
                        });

                        // Prevent dropdown button from collapsing detail row
                        $('.btn').click(function(e) {
                            e.stopPropagation();
                        });

                        // Close other dropdowns when one is opened
                        $('.dropdown-toggle').on('click', function(e) {
                            e.stopPropagation(); // Prevent event bubbling to document

                            // Close any other open dropdowns
                            $('.dropdown-menu.show').removeClass('show');

                            // Toggle this one
                            var $menu = $(this).next('.dropdown-menu');
                            $menu.toggleClass('show');
                        });

                        // Close all dropdowns if clicked outside
                        $(document).on('click', function() {
                            $('.dropdown-menu.show').removeClass('show');
                        });
                    });
                </script>

</body>

</html>