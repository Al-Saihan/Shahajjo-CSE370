<?php require_once 'includes/navbar.php'; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Shahajjo</a>
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['email']) ?>
                </span>
                <a class="nav-link" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>