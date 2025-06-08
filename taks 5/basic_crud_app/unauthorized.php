<?php
require 'db.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unauthorized</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-danger">
            <h4>Access Denied</h4>
            <p>You don't have permission to access this page.</p>
            <a href="read_posts.php" class="btn btn-primary">Return to Home</a>
        </div>
    </div>
</body>
</html>