
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Basic CRUD App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 2rem; background-color: #f8f9fa; }
        .container-custom { max-width: 800px; margin: auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container-custom">

<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$_POST['title'], $_POST['content'], $id]);
    echo "<div class='alert alert-success'>Post updated.</div>";
}
?>
<h2>Edit Post</h2>
<form method="POST">
    <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>"></div>
    <div class="mb-3"><label class="form-label">Content</label><textarea name="content" class="form-control"><?= htmlspecialchars($post['content']) ?></textarea></div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="read_posts.php" class="btn btn-secondary">Back</a>
</form>

</div>
</body>
</html>
