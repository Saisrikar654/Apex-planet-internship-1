<?php
require 'db.php';
session_start();
redirectIfNotAuthorized('editor');

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && verifyCsrfToken($_POST['csrf_token'])) {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $userId = $_SESSION['user_id'];
    
    // Validation
    if (empty($title)) $errors[] = "Title is required";
    if (empty($content)) $errors[] = "Content is required";
    if (strlen($title) > 255) $errors[] = "Title must be less than 255 characters";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $userId]);
            $success = "Post created successfully!";
            // Clear form
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = "Error creating post: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { padding: 2rem; background-color: #f8f9fa; }
        .container-custom {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container-custom">
    <h2 class="mb-4">Create New Post</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <?= htmlspecialchars($error) ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" type="text" class="form-control" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required maxlength="255">
        </div>

        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">Create Post</button>
        <a href="read_posts.php" class="btn btn-secondary">Back to Posts</a>
    </form>
</div>
</body>
</html>