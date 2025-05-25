
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo "<div class='alert alert-danger'>Username already taken.</div>";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);
        echo "<div class='alert alert-success'>Registration successful.</div>";
    }
}
?>
<form method="POST">
    <h2>Register</h2>
    <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control"></div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

</div>
</body>
</html>
