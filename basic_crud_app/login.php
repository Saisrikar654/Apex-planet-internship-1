<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem;
        }
        .form-container { 
            max-width: 450px; 
            width: 100%; 
            margin: auto; 
            background: white; 
            padding: 2.5rem; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        .form-title {
            color: #0d6efd;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .btn-login {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
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
        echo "<div class='alert alert-danger'>Invalid username or password.</div>";
    }
}
?>

<form method="POST">
    <h2 class="form-title text-center">
        <i class="bi bi-box-arrow-in-right"></i> Login
    </h2>
    
    <div class="mb-3">
        <label class="form-label">Username</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input name="username" class="form-control" required>
        </div>
    </div>
    
    <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" required>
        </div>
    </div>
    
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-login">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
    </div>
    
    <div class="mt-3 text-center">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</form>

</div>
</body>
</html>