<?php
require_once 'config.php';
requireAdminLogin();

$message = '';
$error = '';

// Handle blog post actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_post'])) {
        // Create new blog post
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $excerpt = $_POST['excerpt'] ?? '';
        $category = $_POST['category'] ?? 'Trading Psychology';
        $tags = $_POST['tags'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        
        if ($title && $content) {
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
            
            $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, category, tags, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $slug, $content, $excerpt, $category, $tags, $status])) {
                $message = 'Blog post created successfully!';
            } else {
                $error = 'Failed to create blog post.';
            }
        } else {
            $error = 'Title and content are required.';
        }
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
    if ($stmt->execute([$post_id])) {
        $message = 'Blog post deleted successfully!';
    } else {
        $error = 'Failed to delete blog post.';
    }
}

// Get all blog posts
$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Mental Edge Trading Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg: #f3f0ea;
            --bg-soft: #f7f4ee;
            --bg-card: #ffffff;
            --text-main: #1c1c1f;
            --text-muted: #6c6a72;
            --accent: #666A10;
            --accent-soft: #f8e0ea;
            --border-subtle: #e0ddd5;
            --shadow-soft: 0 18px 40px rgba(12, 8, 4, 0.1);
            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-pill: 999px;
            --transition-fast: 0.2s ease-out;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* Header */
        .admin-header {
            background: var(--bg-card);
            box-shadow: var(--shadow-soft);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .username {
            font-weight: 500;
        }

        .logout-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-fast);
        }

        .logout-btn:hover {
            background: #555915;
        }

        /* Main Layout */
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }

        /* Sidebar */
        .sidebar {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-menu li {
            margin-bottom: 8px;
        }

        .nav-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            text-decoration: none;
            color: var(--text-main);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            font-weight: 500;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            background: var(--accent);
            color: white;
        }

        /* Main Content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Messages */
        .message {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Section */
        .section {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-pill);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 106, 16, 0.3);
        }

        .btn.secondary {
            background: var(--text-muted);
        }

        .btn.danger {
            background: #dc3545;
        }

        /* Blog Posts Table */
        .posts-table {
            width: 100%;
            border-collapse: collapse;
        }

        .posts-table th,
        .posts-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-subtle);
        }

        .posts-table th {
            background: var(--bg-soft);
            font-weight: 600;
            color: var(--text-main);
        }

        .posts-table tr:hover {
            background: var(--bg-soft);
        }

        .post-title {
            font-weight: 500;
            color: var(--text-main);
        }

        .post-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons a,
        .action-buttons button {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Blog Form */
        .blog-form {
            display: none;
            margin-top: 20px;
        }

        .blog-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-main);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            background: var(--bg-soft);
            transition: border-color var(--transition-fast);
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-container {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-inner">
            <a href="admin_dashboard.php" class="logo">
                <i class="fas fa-cog"></i> Admin Panel
            </a>
            <div class="user-info">
                <span class="username">
                    <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </span>
                <a href="admin_logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="admin-container">
        <aside class="sidebar">
            <nav>
                <ul class="nav-menu">
                    <li><a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="admin_blog.php" class="active">
                        <i class="fas fa-blog"></i> Blog Management
                    </a></li>
                    <li><a href="admin_analytics.php">
                        <i class="fas fa-chart-line"></i> Analytics
                    </a></li>
                    <li><a href="admin_settings.php">
                        <i class="fas fa-cog"></i> Settings
                    </a></li>
                    <li><a href="index.html" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Website
                    </a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Messages -->
            <?php if ($message): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Blog Management Section -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-blog"></i> Blog Posts Management
                    </h2>
                    <button type="button" class="btn" onclick="toggleBlogForm()">
                        <i class="fas fa-plus"></i> Create New Post
                    </button>
                </div>

                <!-- Blog Creation Form -->
                <div class="blog-form" id="blogForm">
                    <form method="POST">
                        <input type="hidden" name="create_post" value="1">
                        
                        <div class="form-group">
                            <label for="title">Blog Title *</label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="form-group">
                            <label for="excerpt">Excerpt (Short Description)</label>
                            <textarea id="excerpt" name="excerpt" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category">
                                <option value="Trading Psychology">Trading Psychology</option>
                                <option value="Mental Health">Mental Health</option>
                                <option value="Market Analysis">Market Analysis</option>
                                <option value="Personal Development">Personal Development</option>
                                <option value="Trading Strategies">Trading Strategies</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tags">Tags (comma-separated)</label>
                            <input type="text" id="tags" name="tags" placeholder="trading, psychology, mental edge">
                        </div>

                        <div class="form-group">
                            <label for="content">Blog Content *</label>
                            <textarea id="content" name="content" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn">
                                <i class="fas fa-save"></i> Create Post
                            </button>
                            <button type="button" class="btn secondary" onclick="toggleBlogForm()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Blog Posts Table -->
                <table class="posts-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                                <div class="post-meta">By <?php echo htmlspecialchars($post['author']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($post['category']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($post['views']); ?></td>
                            <td>
                                <div class="post-meta">
                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="admin_edit_blog.php?id=<?php echo $post['id']; ?>" class="btn secondary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $post['id']; ?>" 
                                       class="btn danger" 
                                       onclick="return confirm('Are you sure you want to delete this post?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function toggleBlogForm() {
            const form = document.getElementById('blogForm');
            form.classList.toggle('active');
        }
    </script>
</body>
</html>