<?php
include '../../includes/header.php';
requireAdmin();

// Handle organization actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_organization'])) {
        $name = sanitize($_POST['name']);
        $bin = sanitize($_POST['bin']);
        $branch = sanitize($_POST['branch']);
        $account = sanitize($_POST['account']);

        $sql = "INSERT INTO Organization_table (Org_BIN, Name, Branch, Account)
                VALUES ('$bin', '$name', '$branch', '$account')";
        $conn->query($sql);
    }

    if (isset($_POST['delete'])) {
        $bin = sanitize($_POST['bin']);
        $conn->query("DELETE FROM Organization_table WHERE Org_BIN = '$bin'");
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manage Organizations</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrgModal">
        <i class="fas fa-plus"></i> Add Organization
    </button>
</div>

<div class="card shadow-sm rounded-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>BIN</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Account</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM Organization_table";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>
                                    <td>' . $row['Org_BIN'] . '</td>
                                    <td>' . $row['Name'] . '</td>
                                    <td>' . $row['Branch'] . '</td>
                                    <td>' . $row['Account'] . '</td>
                                    <td>
                                        <form method="post" style="display:inline">
                                            <input type="hidden" name="bin" value="' . $row['Org_BIN'] . '">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                  </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5">No organizations found</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Organization Modal -->
<div class="modal fade" id="addOrgModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Organization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">BIN Number</label>
                        <input type="text" class="form-control" name="bin" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Organization Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" class="form-control" name="branch">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-control" name="account" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_organization" class="btn btn-primary">Save Organization</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>