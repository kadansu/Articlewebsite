<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
require_once 'database.php';

$userId = $_SESSION['userid'];
$error = '';
$success = '';

$database = new Database();
$conn = $database->conn;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $address = $_POST['address'];
    $newPassword = $_POST['new_password'];

    // Start with a basic update query
    $query = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ?";
    $params = [$fullName, $email, $phoneNumber, $address, $userId];
    $types = "ssssi";

    // If a new password is provided, hash and include it
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query .= ", password = ?";
        $params = [$fullName, $email, $phoneNumber, $address, $hashedPassword, $userId];
        $types = "sssssi";
    }

    $query .= " WHERE userid = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $success = 'Profile updated successfully.';
        // Update session variable for full name
        $_SESSION['full_name'] = $fullName;
    } else {
        $error = 'Error updating profile: ' . $stmt->error;
    }

    $stmt->close();
}

// Fetch user data for the form
$stmt = $conn->prepare("SELECT * FROM users WHERE userid = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Update My Profile</h1>
        <div class="auth-info">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Edit Your Details</h2>
        <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
        <div class="form-container">
            <form action="update_profile.php" method="POST">
                <label for="username">Username (Cannot be changed):</label>
                <input type="text" id="username" value="<?= htmlspecialchars($user['user_name']) ?>" disabled>

                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>">

                <label for="new_password">New Password (leave blank to keep current):</label>
                <input type="password" id="new_password" name="new_password">

                <input type="submit" value="Update Profile">
            </form>
        </div>
    </div>
</body>
</html>