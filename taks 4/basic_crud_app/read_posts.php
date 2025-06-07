<?php
require 'db.php';
session_start();
redirectIfNotLoggedIn();

// Search functionality
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE p.title LIKE ? OR p.content LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Pagination
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total posts for pagination
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM posts p JOIN users u ON p.user_id = u.id $where");
$totalStmt->execute($params);
$totalPosts = $totalStmt->fetchColumn();

// Get posts for current page
$query = "SELECT p.*, u.username, u.role as user_role FROM posts p JOIN users u ON p.user_id = u.id $where ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($query);
$paramsWithLimit = array_merge($params, [$perPage, $offset]);
$stmt->execute($paramsWithLimit);
$posts = $stmt->fetchAll();

$totalPages = ceil($totalPosts / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { padding: 2rem; background-color: #f8f9fa; }
        .container-custom { 
            max-width: 1000px; 
            margin: auto; 
            background: white; 
            padding: 2rem; 
            border-radius: 10px; 
            box-shadow: 0 0 15px rgba(0,0,0,0.1); 
        }
        .post-card {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        .post-card:hover {
            transform: translateY(-3px);
        }
        .search-box {
            max-width: 400px;
        }
        .author-badge {
            font-size: 0.8rem;
            background-color:rgb(23, 29, 24);
        }
    </style>
</head>
<body>
<div class="container-custom">

    <!-- Navigation and Logout -->
    <div class="d-flex justify-content-between mb-4">
        <h2>Blog Posts</h2>
        <div>
            <?php if (hasPermission('editor')): ?>
                <a href="create_post.php" class="btn btn-success me-2">
                    <i class="bi bi-plus-lg"></i> Create New Post
                </a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="alert alert-info mb-4">
        Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>! 
        (Role: <?= htmlspecialchars(ucfirst($_SESSION['user_role'] ?? 'user')) ?>)
    </div>

    <!-- Search Form -->
    <form method="GET" class="mb-4">
        <div class="input-group search-box">
            <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i> Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="read_posts.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i> Clear
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Posts Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <?= empty($search) ? 'No posts found.' : 'No posts match your search.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                    <tr class="post-card">
                        <td><?= htmlspecialchars($post['title']) ?></td>
                        <td><?= htmlspecialchars(substr($post['content'], 0, 100)) . (strlen($post['content']) > 100 ? '...' : '') ?></td>
                        <td>
                            <span class="badge author-badge">
                                <?= htmlspecialchars($post['username']) ?>
                                <?php if ($post['user_role'] === 'admin'): ?>
                                    <i class="bi bi-shield-fill text-danger"></i>
                                <?php elseif ($post['user_role'] === 'editor'): ?>
                                    <i class="bi bi-pencil-fill text-primary"></i>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><?= date('M j, Y g:i a', strtotime($post['created_at'])) ?></td>
                        <td>
                            <?php if ($post['user_id'] == $_SESSION['user_id'] || hasPermission('admin')): ?>
                                <a href="update_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            <?php endif; ?>
                            <?php if (hasPermission('admin')): ?>
                                <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

</div>
</body>
</html>