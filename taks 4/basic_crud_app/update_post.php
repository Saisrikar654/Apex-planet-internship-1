
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
// Add this after session_start()
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    // Validation
    $errors = [];
    if (empty($username)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: read_posts.php");
            exit;
        } else {
            $errors[] = "Invalid username or password";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: login.php");
        exit;
    }
}

// Display errors at the top of the form
if (isset($_SESSION['form_errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['form_errors'] as $error) {
        echo htmlspecialchars($error) . '<br>';
    }
    echo '</div>';
    unset($_SESSION['form_errors']);
}

// Pre-fill form if available
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

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
