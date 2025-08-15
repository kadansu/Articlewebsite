<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['userid']) || $_SESSION['usertype'] !== 'author') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';
$database = new Database();
$conn = $database->conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authorId = $_SESSION['userid'];
    $title = $_POST['article_title'];
    $fullText = $_POST['article_full_text'];
    $display = $_POST['article_display'];
    $order = $_POST['article_order'];

    $stmt = $conn->prepare("INSERT INTO articles (authorid, article_title, article_full_text, article_display, article_order) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issis", $authorId, $title, $fullText, $display, $order);

    if ($stmt->execute()) {
        $success = "Article **" . htmlspecialchars($title) . "** added successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Article</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Add New Article</h1>
        <div class="auth-info">
            <a href="manage_articles.php" class="btn">Back to Articles</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Create a New Article</h2>
        <?php if ($success): ?><p style="color: green;"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>
        <div class="form-container">
            <form action="add_article.php" method="POST">
                <label for="article_title">Title:</label>
                <input type="text" id="article_title" name="article_title" required>

                <label for="article_full_text">Full Text:</label>
                <textarea id="article_full_text" name="article_full_text" rows="10" required></textarea>

                <label for="article_display">Display:</label>
                <select id="article_display" name="article_display">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>

                <label for="article_order">Order:</label>
                <input type="number" id="article_order" name="article_order" value="0">

                <input type="submit" value="Add Article">
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>