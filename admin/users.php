<?php
include '../../includes/header.php';
requireAdmin();

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = sanitize($_GET['delete']);
    $conn->query("DELETE FROM User_table WHERE UID = $user_id");
    $_SESSION['message'] = "User deleted successfully";
    $_SESSION['message_type'] = "success";
    header("Location: users.php");
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Users</h2>
    <a href="add_user.php" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add User
    </a>
</div>

<div class="card shadow-sm rounded-lg">
    <div class="card-body">
        <?php displayAlert(); ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM User_table ORDER BY Reg_date DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                    <td>' . $row['UID'] . '</td>
                                    <td>' . $row['F_name'] . ' ' . $row['L_Name'] . '</td>
                                    <td>' . $row['email'] . '</td>
                                    <td><span class="badge bg-primary">' . ucfirst($row['user_type']) . '</span></td>
                                    <td>' . date('M d, Y', strtotime($row['Reg_date'])) . '</td>
                                    <td>
                                        <a href="edit_user.php?id=' . $row['UID'] . '" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDelete(' . $row['UID'] . ')" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                  </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No users found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            window.location.href = 'users.php?delete=' + userId;
        }
    }
</script>

<?php include '../../includes/footer.php'; ?>