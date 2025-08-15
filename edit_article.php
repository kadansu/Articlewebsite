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
$error = '';
$success = '';
$database = new Database();
$conn = $database->conn;

// Fetch article data and check ownership
$stmt = $conn->prepare("SELECT * FROM articles WHERE articleid = ? AND authorid = ?");
$stmt->bind_param("ii", $articleId, $authorId);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$article) {
    $_SESSION['error'] = 'You do not have permission to edit this article.';
    header("Location: manage_articles.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['article_title'];
    $fullText = $_POST['article_full_text'];
    $display = $_POST['article_display'];
    $order = $_POST['article_order'];

    $stmt = $conn->prepare("UPDATE articles SET article_title = ?, article_full_text = ?, article_display = ?, article_order = ? WHERE articleid = ? AND authorid = ?");
    $stmt->bind_param("sssiii", $title, $fullText, $display, $order, $articleId, $authorId);

    if ($stmt->execute()) {
        $success = "Article **" . htmlspecialchars($title) . "** updated successfully!";
    } else {
        $error = "Error updating article: " . $stmt->error;
    }
    $stmt->close();
}
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Article</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Edit Article: <?= htmlspecialchars($article['article_title']) ?></h1>
        <div class="auth-info">
            <a href="manage_articles.php" class="btn">Back to Articles</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="container">
        <h2>Edit Article Details</h2>
        <?php if ($success): ?><p style="color: green;"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>
        <div class="form-container">
            <form action="edit_article.php?id=<?= htmlspecialchars($articleId) ?>" method="POST">
                <label for="article_title">Title:</label>
                <input type="text" id="article_title" name="article_title" value="<?= htmlspecialchars($article['article_title']) ?>" required>

                <label for="article_full_text">Full Text:</label>
                <textarea id="article_full_text" name="article_full_text" rows="10" required><?= htmlspecialchars($article['article_full_text']) ?></textarea>

                <label for="article_display">Display:</label>
                <select id="article_display" name="article_display">
                    <option value="yes" <?= $article['article_display'] === 'yes' ? 'selected' : '' ?>>Yes</option>
                    <option value="no" <?= $article['article_display'] === 'no' ? 'selected' : '' ?>>No</option>
                </select>

                <label for="article_order">Order:</label>
                <input type="number" id="article_order" name="article_order" value="<?= htmlspecialchars($article['article_order']) ?>">

                <input type="submit" value="Update Article">
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 CMS System</p>
    </footer>
</body>
</html>