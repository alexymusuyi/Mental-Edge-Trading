<?php
require_once 'config.php';
requireAdminLogin();

// Get analytics data
$analytics = [];

// Get last 30 days analytics
$stmt = $pdo->query("SELECT * FROM site_analytics ORDER BY date DESC LIMIT 30");
$analytics['daily'] = $stmt->fetchAll();

// Get total statistics
$stmt = $pdo->query("SELECT SUM(page_views) as total_page_views, SUM(unique_visitors) as total_visitors, SUM(blog_views) as total_blog_views FROM site_analytics");
$analytics['totals'] = $stmt->fetch();

// Get popular blog posts
$stmt = $pdo->query("SELECT title, views FROM blog_posts WHERE status = 'published' ORDER BY views DESC LIMIT 10");
$analytics['popular_posts'] = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Mental Edge Trading Admin</title>
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

        /* Stats Grid */
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

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 20px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        /* Popular Posts List */
        .popular-posts {
            list-style: none;
        }

        .popular-posts li {
            padding: 12px 0;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .popular-posts li:last-child {
            border-bottom: none;
        }

        .post-info {
            flex: 1;
        }

        .post-title {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .post-views {
            color: var(--accent);
            font-weight: 600;
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
            
            .chart-grid {
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
                    <li><a href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="admin_blog.php">
                        <i class="fas fa-blog"></i> Blog Management
                    </a></li>
                    <li><a href="admin_analytics.php" class="active">
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
            <!-- Analytics Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($analytics['totals']['total_page_views'] ?? 0); ?></div>
                    <div class="stat-label">Total Page Views</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($analytics['totals']['total_visitors'] ?? 0); ?></div>
                    <div class="stat-label">Unique Visitors</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($analytics['totals']['total_blog_views'] ?? 0); ?></div>
                    <div class="stat-label">Blog Views</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number"><?php echo count($analytics['daily']); ?></div>
                    <div class="stat-label">Days Tracked</div>
                </div>
            </div>

            <!-- Traffic Analytics -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-chart-area"></i> Traffic Analytics (Last 30 Days)
                    </h2>
                </div>
                <div class="chart-container">
                    <canvas id="trafficChart"></canvas>
                </div>
            </div>

            <!-- Popular Content -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-star"></i> Popular Blog Posts
                    </h2>
                </div>
                
                <div class="chart-grid">
                    <div>
                        <h3 style="margin-bottom: 20px; color: var(--accent);">Most Viewed Posts</h3>
                        <ul class="popular-posts">
                            <?php foreach ($analytics['popular_posts'] as $post): ?>
                            <li>
                                <div class="post-info">
                                    <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                                </div>
                                <div class="post-views"><?php echo number_format($post['views']); ?> views</div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="popularPostsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Traffic Chart
        const trafficCtx = document.getElementById('trafficChart').getContext('2d');
        
        // Sample data for the last 30 days
        const trafficData = {
            labels: [
                'Nov 1', 'Nov 2', 'Nov 3', 'Nov 4', 'Nov 5', 'Nov 6', 'Nov 7',
                'Nov 8', 'Nov 9', 'Nov 10', 'Nov 11', 'Nov 12', 'Nov 13', 'Nov 14',
                'Nov 15', 'Nov 16', 'Nov 17', 'Nov 18', 'Nov 19', 'Nov 20', 'Nov 21',
                'Nov 22', 'Nov 23', 'Nov 24', 'Nov 25', 'Nov 26', 'Nov 27', 'Nov 28',
                'Nov 29', 'Nov 30'
            ],
            datasets: [{
                label: 'Page Views',
                data: [120, 135, 148, 162, 178, 195, 210, 225, 240, 255, 270, 285, 300, 315, 330, 345, 360, 375, 390, 405, 420, 435, 450, 465, 480, 495, 510, 525, 540, 555],
                borderColor: '#666A10',
                backgroundColor: 'rgba(102, 106, 16, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Unique Visitors',
                data: [85, 92, 98, 105, 112, 118, 125, 132, 138, 145, 152, 158, 165, 172, 178, 185, 192, 198, 205, 212, 218, 225, 232, 238, 245, 252, 258, 265, 272, 278],
                borderColor: '#6c6a72',
                backgroundColor: 'rgba(108, 106, 114, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(trafficCtx, {
            type: 'line',
            data: trafficData,
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

        // Popular Posts Chart
        const popularCtx = document.getElementById('popularPostsChart').getContext('2d');
        
        new Chart(popularCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Stop Revenge Trading',
                    'FOMO Recovery Guide',
                    'Trading Discipline',
                    'Mental Edge Secrets',
                    'Risk Management'
                ],
                datasets: [{
                    data: [1250, 980, 850, 720, 650],
                    backgroundColor: [
                        '#666A10',
                        '#6c6a72',
                        '#8b8b8b',
                        '#a5a5a5',
                        '#bfbfbf'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>