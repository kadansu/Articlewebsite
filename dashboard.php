<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
$usertype = $_SESSION['usertype'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
        <div class="auth-info">
            <span>Role: <?= htmlspecialchars($usertype) ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Your Dashboard</h2>
        <div class="dashboard-links">
            <a href="update_profile.php" class="dashboard-link">Update My Profile</a>
            <a href="view_articles.php" class="dashboard-link">View Articles</a>

            <?php if ($usertype === 'super_user'): ?>
                <a href="manage_users.php" class="dashboard-link">Manage Other Users</a>
            <?php endif; ?>

            <?php if ($usertype === 'administrator'): ?>
                <a href="manage_users.php?type=author" class="dashboard-link">Manage Authors</a>
            <?php endif; ?>

            <?php if ($usertype === 'author'): ?>
                <a href="manage_articles.php" class="dashboard-link">Manage My Articles</a>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>