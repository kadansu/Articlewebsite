<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['userid']) || ($_SESSION['usertype'] !== 'super_user' && $_SESSION['usertype'] !== 'administrator')) {
    header("Location: login.php");
    exit();
}

$userId = $_GET['id'] ?? null;
if (!$userId) {
    header("Location: manage_users.php");
    exit();
}

$error = '';
$success = '';
$database = new Database();
$conn = $database->conn;

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE userid = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || ($_SESSION['usertype'] === 'administrator' && $user['usertype'] !== 'author')) {
    $_SESSION['error'] = 'You do not have permission to edit this user.';
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $userType = $_POST['usertype'];
    $address = $_POST['address'];
    $newPassword = $_POST['new_password'];
    
    // Build query dynamically
    $query = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, usertype = ?, address = ?";
    $params = [$fullName, $email, $phoneNumber, $userType, $address, $userId];
    $types = "sssssi";

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params = [$fullName, $email, $phoneNumber, $userType, $address, $hashedPassword, $userId];
        $types = "ssssssi";
    }

    $query .= " WHERE userid = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success = "User **" . htmlspecialchars($user['user_name']) . "** updated successfully.";
    } else {
        $error = "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Edit User: <?= htmlspecialchars($user['user_name']) ?></h1>
        <div class="auth-info">
            <a href="manage_users.php" class="btn">Back to Users</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Edit User Details</h2>
        <?php if ($success): ?><p style="color: green;"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>
        <div class="form-container">
            <form action="edit_user.php?id=<?= htmlspecialchars($userId) ?>" method="POST">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>">

                <label for="usertype">User Type:</label>
                <select id="usertype" name="usertype" required>
                    <?php if ($_SESSION['usertype'] === 'super_user'): ?>
                        <option value="super_user" <?= $user['usertype'] === 'super_user' ? 'selected' : '' ?>>Super User</option>
                        <option value="administrator" <?= $user['usertype'] === 'administrator' ? 'selected' : '' ?>>Administrator</option>
                    <?php endif; ?>
                    <option value="author" <?= $user['usertype'] === 'author' ? 'selected' : '' ?>>Author</option>
                </select>

                <label for="new_password">New Password (leave blank to keep current):</label>
                <input type="password" id="new_password" name="new_password">

                <input type="submit" value="Update User">
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>