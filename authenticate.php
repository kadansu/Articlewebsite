<?php
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $database = new Database();
    $conn = $database->conn;

    $stmt = $conn->prepare("SELECT userid, full_name, usertype, password FROM users WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['userid'] = $user['userid'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['usertype'] = $user['usertype'];
            header("Location: dashboard.php");
            exit();
        }
    }

    $_SESSION['error'] = 'Invalid username or password.';
    header("Location: dashboard.php");
    exit();

    $stmt->close();
    $database->closeConnection();
}
?>