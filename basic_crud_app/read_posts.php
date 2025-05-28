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
    </style>
</head>
<body>
<div class="container-custom">

<?php
require 'db.php';
session_start();

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE title LIKE ? OR content LIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Pagination
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total posts for pagination
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM posts $where");
$totalStmt->execute($params);
$totalPosts = $totalStmt->fetchColumn();

// Get posts for current page
$stmt = $pdo->prepare("SELECT * FROM posts $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$paramsWithLimit = array_merge($params, [$perPage, $offset]);
$stmt->execute($paramsWithLimit);
$posts = $stmt->fetchAll();

$totalPages = ceil($totalPosts / $perPage);
?>

<h2 class="mb-4 d-flex justify-content-between align-items-center">
    <span>Blog Posts</span>
    <a href="create_post.php" class="btn btn-success">
        <i class="bi bi-plus-lg"></i> Create New Post
    </a>
</h2>

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
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <?= empty($search) ? 'No posts found.' : 'No posts match your search.' ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <tr class="post-card">
                    <td><?= htmlspecialchars($post['title']) ?></td>
                    <td><?= htmlspecialchars(substr($post['content'], 0, 100)) . (strlen($post['content']) > 100 ? '...' : '') ?></td>
                    <td><?= date('M j, Y g:i a', strtotime($post['created_at'])) ?></td>
                    <td>
                        <a href="update_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="bi bi-trash"></i> Delete
                        </a>
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