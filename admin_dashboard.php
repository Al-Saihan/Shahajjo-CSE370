<?php
require_once 'includes/config.php';
session_start();

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Get all users with complete details
try {
    $stmt = $pdo->query("
        SELECT 
            u.id, u.first_name, u.middle_name, u.last_name, u.email,
            u.role, u.created_at, u.status, u.admin_id,
            d.address AS donor_address, d.contact_number AS donor_contact,
            r.address AS recipient_address, r.contact_number AS recipient_contact
        FROM user_table u
        LEFT JOIN donor_table d ON u.id = d.user_id
        LEFT JOIN recipient_table r ON u.id = r.user_id
        ORDER BY u.id ASC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error 1: " . $e->getMessage());
}

// Get admin details for the currently logged-in admin
try {
    $stmt = $pdo->prepare("
        SELECT 
            admin_id, access_level
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
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
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
            <a class="navbar-brand auth-logo fw-bold text-white fs-3" href="index.php">Shahajjo</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Admin ID: <?= $_SESSION['user_id'] ?>
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">

        <h2 class="mb-4 d-flex justify-content-between align-items-center">
            User Management
            <a href="add_admin.php" class="btn btn-danger btn-sm">Add Admin</a>
        </h2>

        <div class="table-container"> <!-- Added table-container class -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-center">
                    <tr class="table-dark">
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
                                </td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div>
                                        <!-- MANAGE BUTTON -->
                                        <button class="btn btn-sm btn-warning dropdown-toggle border border-dark" type="button" id="manageDropdown<?= $user['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu bg-dark text-white" aria-labelledby="manageDropdown<?= $user['id'] ?>">
                                            <!-- CHANGE STATUS/VERIFICATION -->
                                            <li>
                                                <form action="verify_user.php" method="POST" style="margin: 0;">
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
                                                <form action="blacklist_user.php" method="POST" style="margin: 0;">
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

                                            <!-- CHANGE ROLE SECTION -->
                                            <li>
                                                <form action="change_role.php" method="POST" style="margin: 0;">
                                                    <?php if ($user['id'] === $admin_id): ?>
                                                        <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">You cannot change your own Role</div>
                                                        <!-- CHECK IF CURRENTE ADMIN IS SUPER ADMIN -->
                                                    <?php elseif ($admin['access_level'] !== 'super_admin'): ?>
                                                        <div class="dropdown-item text-danger bg-dark border-bottom border-secondary">Only Super Admin can change roles</div>
                                                    <?php else: ?>
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" class="dropdown-item text-white bg-dark border-bottom border-secondary">Change Role</button>
                                                    <?php endif; ?>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr class="detail-row">
                                <td colspan="7" class="p-0">
                                    <div id="details-<?= $user['id'] ?>" class="collapse">
                                        <div class="user-details">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5>Basic Information</h5>
                                                    <p><strong>Full Name:</strong>
                                                        <?= htmlspecialchars($user['first_name'] . ' ' .
                                                            ($user['middle_name'] ? $user['middle_name'] . ' ' : '') .
                                                            $user['last_name']) ?>
                                                    </p>
                                                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                                    <p><strong>Account Type:</strong> <?= ucfirst($user['role']) ?></p>
                                                    <p><strong>Registration Date:</strong> <?= date('F j, Y, g:i a', strtotime($user['created_at'])) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5>Contact Information</h5>
                                                    <?php if ($user['role'] === 'donor'): ?>
                                                        <div class="address-box">
                                                            <p><strong>Address:</strong></p>
                                                            <?= $user['donor_address'] ? nl2br(htmlspecialchars($user['donor_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                                        </div>
                                                        <p><strong>Phone Number:</strong> <?= $user['donor_contact'] ? htmlspecialchars($user['donor_contact']) : '<span class="text-muted">Not provided</span>' ?></p>
                                                    <?php elseif ($user['role'] === 'recipient'): ?>
                                                        <div class="address-box">
                                                            <p><strong>Address:</strong></p>
                                                            <?= $user['recipient_address'] ? nl2br(htmlspecialchars($user['recipient_address'])) : '<p class="text-muted">Not provided</p>' ?>
                                                        </div>
                                                        <p><strong>Phone Number:</strong> <?= $user['recipient_contact'] ? htmlspecialchars($user['recipient_contact']) : '<span class="text-muted">Not provided</span>' ?></p>
                                                    <?php endif; ?>
                                                </div>
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

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- <script>
            $(document).ready(function() {
                // Make rows clickable to toggle details
                $('tr[data-toggle="collapse"]').click(function() {
                    $(this).next('tr').find('.collapse').collapse('toggle');
                });

                // Prevent action buttons from triggering row collapse
                $('.btn').click(function(e) {
                    e.stopPropagation();
                });
            });
        </script> -->

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