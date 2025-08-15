<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'author') {
    header("Location: index.php");
    exit();
}

$articleId = $_GET['id'] ?? null;
if (!$articleId) {
    header("Location: manage_articles.php");
    exit();
}

$authorId = $_SESSION['userid'];
$database = new Database();
$conn = $database->conn;

// Verify ownership before deleting
$stmt = $conn->prepare("DELETE FROM articles WHERE articleid = ? AND authorid = ?");
$stmt->bind_param("ii", $articleId, $authorId);
$stmt->execute();
$stmt->close();
$database->closeConnection();

header("Location: manage_articles.php");
exit();
?>