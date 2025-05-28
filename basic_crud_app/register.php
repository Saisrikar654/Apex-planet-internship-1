<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
        .btn-register {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="form-container">

<?php
require 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-danger'>Username already taken. Please choose another.</div>";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        echo "<div class='alert alert-success'>Registration successful! You can now <a href='login.php'>login</a>.</div>";
    }
}
?>

<form method="POST">
    <h2 class="form-title text-center">
        <i class="bi bi-person-plus"></i> Register
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
        <button type="submit" class="btn btn-primary btn-register">
            <i class="bi bi-person-plus"></i> Register
        </button>
    </div>
    
    <div class="mt-3 text-center">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</form>

</div>
</body>
</html>
