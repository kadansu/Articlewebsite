<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'author') {
    header("Location: index.php");
    exit();
}
require_once 'database.php';

$authorId = $_SESSION['userid'];
$database = new Database();
$conn = $database->conn;

$query = "SELECT * FROM articles WHERE authorid = ? ORDER BY article_created_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $authorId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Articles</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Manage My Articles</h1>
        <div class="auth-info">
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Your Articles</h2>
        <div class="button-group">
            <a href="add_article.php" class="btn add">Add New Article</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($article = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($article['articleid']) ?></td>
                        <td><?= htmlspecialchars($article['article_title']) ?></td>
                        <td><?= date('Y-m-d', strtotime($article['article_created_date'])) ?></td>
                        <td class="action-links">
                            <a href="edit_article.php?id=<?= htmlspecialchars($article['articleid']) ?>">Edit</a> |
                            <a href="delete_article.php?id=<?= htmlspecialchars($article['articleid']) ?>" onclick="return confirm('Are you sure?')">Delete</a>
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