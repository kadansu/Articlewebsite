<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}
require_once 'database.php';

$database = new Database();
$conn = $database->conn;

$query = "SELECT articles.*, users.full_name FROM articles JOIN users ON articles.authorid = users.userid ORDER BY article_created_date DESC LIMIT 6";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Articles</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Latest Articles</h1>
        <div class="auth-info">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Last 6 Articles</h2>
        <div class="article-cards">
            <?php while ($article = $result->fetch_assoc()): ?>
                <div class="article-card">
                    <h3><?= htmlspecialchars($article['article_title']) ?></h3>
                    <p>By: <?= htmlspecialchars($article['full_name']) ?></p>
                    <p><small>Published on: <?= date('F j, Y', strtotime($article['article_created_date'])) ?></small></p>
                    <p><?= htmlspecialchars(substr($article['article_full_text'], 0, 150)) ?>...</p>
                    <a href="#" class="btn" style="padding: 10px 15px;">Read More</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>