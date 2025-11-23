<?php
require_once 'config.php';
requireAdminLogin();

// Get dashboard statistics
$stats = [];

// Total blog posts
$stmt = $pdo->query("SELECT COUNT(*) as total FROM blog_posts");
$stats['total_posts'] = $stmt->fetchColumn();

// Published posts
$stmt = $pdo->query("SELECT COUNT(*) as published FROM blog_posts WHERE status = 'published'");
$stats['published_posts'] = $stmt->fetchColumn();

// Draft posts
$stmt = $pdo->query("SELECT COUNT(*) as drafts FROM blog_posts WHERE status = 'draft'");
$stats['draft_posts'] = $stmt->fetchColumn();

// Total views
$stmt = $pdo->query("SELECT SUM(views) as total_views FROM blog_posts");
$stats['total_views'] = $stmt->fetchColumn() ?? 0;

// Recent posts
$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5");
$recent_posts = $stmt->fetchAll();

// Get today's analytics
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM site_analytics WHERE date = ?");
$stmt->execute([$today]);
$today_stats = $stmt->fetch();

if (!$today_stats) {
    $today_stats = ['page_views' => 0, 'unique_visitors' => 0, 'blog_views' => 0, 'contact_forms' => 0];
}

// Get all blog posts for management
$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
$all_posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mental Edge Trading</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
            text-align: center;
            transition: transform var(--transition-fast);
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
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

        /* Analytics Chart */
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
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
            
            .stats-grid {
                grid-template-columns: 1fr;
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
                    <li><a href="admin_dashboard.php" class="active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="admin_blog.php">
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
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total_posts']; ?></div>
                    <div class="stat-label">Total Blog Posts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['published_posts']; ?></div>
                    <div class="stat-label">Published Posts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['draft_posts']; ?></div>
                    <div class="stat-label">Draft Posts</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                    <div class="stat-label">Total Views</div>
                </div>
            </div>

            <!-- Today's Analytics -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-chart-bar"></i> Today's Analytics
                    </h2>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $today_stats['page_views']; ?></div>
                        <div class="stat-label">Page Views Today</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $today_stats['unique_visitors']; ?></div>
                        <div class="stat-label">Unique Visitors</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $today_stats['blog_views']; ?></div>
                        <div class="stat-label">Blog Views</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $today_stats['contact_forms']; ?></div>
                        <div class="stat-label">Contact Forms</div>
                    </div>
                </div>
            </div>

            <!-- Recent Blog Posts -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-clock"></i> Recent Blog Posts
                    </h2>
                    <a href="admin_blog.php" class="btn">
                        <i class="fas fa-plus"></i> New Post
                    </a>
                </div>

                <table class="posts-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_posts as $post): ?>
                        <tr>
                            <td>
                                <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                                <div class="post-meta">By <?php echo htmlspecialchars($post['author']); ?></div>
                            </td>
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
                                    <a href="admin_blog.php?delete=<?php echo $post['id']; ?>" 
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

            <!-- Performance Chart -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-chart-line"></i> Website Performance
                    </h2>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Performance Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        // Sample data - in real implementation, this would come from your analytics
        const chartData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Page Views',
                data: [1200, 1900, 3000, 5000, 4200, 6100],
                borderColor: '#666A10',
                backgroundColor: 'rgba(102, 106, 16, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Blog Views',
                data: [800, 1200, 1800, 2800, 2400, 3500],
                borderColor: '#6c6a72',
                backgroundColor: 'rgba(108, 106, 114, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>