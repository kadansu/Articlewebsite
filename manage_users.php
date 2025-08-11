<?php
session_start();
if (!isset($_SESSION['userid']) || ($_SESSION['usertype'] !== 'super_user' && $_SESSION['usertype'] !== 'administrator')) {
    header("Location: login.php");
    exit();
}
require_once 'database.php';

$usertype = $_SESSION['usertype'];
$database = new Database();
$conn = $database->conn;

// Determine which users to display
$allowedUserTypes = ['administrator', 'author'];
$title = 'Manage All Users';
$query = "SELECT * FROM users";

if ($usertype === 'administrator') {
    $allowedUserTypes = ['author'];
    $title = 'Manage Authors';
    $query .= " WHERE usertype = 'author'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($title) ?></h1>
        <div class="auth-info">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2><?= htmlspecialchars($title) ?></h2>
        <div class="button-group">
            <a href="add_user.php" class="btn add">Add New User</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <?php if ($usertype === 'administrator' && $user['usertype'] !== 'author') continue; ?>
                    <tr>
                        <td><?= htmlspecialchars($user['userid']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['user_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['usertype']) ?></td>
                        <td class="action-links">
                            <a href="edit_user.php?id=<?= htmlspecialchars($user['userid']) ?>">Edit</a> |
                            <a href="delete_user.php?id=<?= htmlspecialchars($user['userid']) ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>