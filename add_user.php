<?php
session_start();
require_once 'database.php';

// Check if user is logged in and has appropriate permissions
if (!isset($_SESSION['userid']) || !in_array($_SESSION['usertype'], ['super_user', 'administrator'])) {
    header("Location: login.php");
    exit();
}


$error = '';
$success = '';
$database = new Database();
$conn = $database->conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phone_number'];
    $userName = $_POST['user_name'];
    $password = $_POST['password'];
    $userType = $_POST['usertype'];
    $address = $_POST['address'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Permission check for user type creation
    if ($_SESSION['usertype'] === 'administrator' && $userType !== 'author') {
        $error = "Administrators can only create 'Author' users.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, user_name, password, usertype, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fullName, $email, $phoneNumber, $userName, $hashedPassword, $userType, $address);

        if ($stmt->execute()) {
            $success = "User **" . htmlspecialchars($userName) . "** added successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add New User</h1>
        <div class="auth-info">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Add a New User</h2>
        <?php if ($success): ?><p style="color: green;"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>
        <div class="form-container">
            <form action="add_user.php" method="POST">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number">

                <label for="user_name">Username:</label>
                <input type="text" id="user_name" name="user_name" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="usertype">User Type:</label>
                <select id="usertype" name="usertype" required>
                    <?php if ($_SESSION['usertype'] === 'super_user'): ?>
                        <option value="super_user">Super User</option>
                        <option value="administrator">Administrator</option>
                    <?php endif; ?>
                    <option value="author">Author</option>
                </select>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address">

                <input type="submit" value="Add User">
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>