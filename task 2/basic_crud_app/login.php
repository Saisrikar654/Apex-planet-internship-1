
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Basic CRUD App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 2rem; background-color: #f8f9fa; }
        .form-container { max-width: 500px; margin: auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="form-container">

<?php
require 'db.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: read_posts.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Invalid login.</div>";
    }
}
?>
<form method="POST">
    <h2>Login</h2>
    <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

</div>
</body>
</html>
