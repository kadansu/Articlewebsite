<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['userid']) || ($_SESSION['usertype'] !== 'super_user' && $_SESSION['usertype'] !== 'administrator')) {
    header("Location: index.php");
    exit();
}

$userId = $_GET['id'] ?? null;
if (!$userId) {
    header("Location: manage_users.php");
    exit();
}

$database = new Database();
$conn = $database->conn;

// Check if the user to be deleted is a super_user or if the current user is an administrator trying to delete a non-author
$stmt = $conn->prepare("SELECT usertype FROM users WHERE userid = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userToDelete = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$userToDelete || ($userToDelete['usertype'] === 'super_user' && $_SESSION['usertype'] !== 'super_user') || ($_SESSION['usertype'] === 'administrator' && $userToDelete['usertype'] !== 'author')) {
    $_SESSION['error'] = 'You do not have permission to delete this user.';
    header("Location: manage_users.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE userid = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->close();
$database->closeConnection();

header("Location: manage_users.php");
exit();
?>