
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
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<h2>All Posts</h2>
<a href="create_post.php" class="btn btn-success mb-3">+ Create New Post</a>
<table class="table table-bordered">
    <thead>
        <tr><th>Title</th><th>Content</th><th>Created At</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= htmlspecialchars($post['title']) ?></td>
            <td><?= htmlspecialchars($post['content']) ?></td>
            <td><?= $post['created_at'] ?></td>
            <td>
                <a href="update_post.php?id=<?= $post['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>
</body>
</html>
