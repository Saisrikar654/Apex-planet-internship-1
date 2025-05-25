<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $post_id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    header("Location: read_posts.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: read_posts.php");
    exit;
}

$post_id = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .container { max-width: 600px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container text-center">
    <h3 class="mb-4 text-danger">Are you sure you want to delete this post?</h3>
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($post_id) ?>">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="read_posts.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
