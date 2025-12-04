<?php
require_once 'config.php';
requireUserLogin();

$user = getCurrentUser();

// Get user's downloads
$stmt = $pdo->prepare("SELECT p.*, ud.download_count, ud.last_download 
                      FROM products p 
                      JOIN orders o ON p.id = o.product_id 
                      JOIN user_downloads ud ON p.id = ud.product_id 
                      WHERE o.user_id = ? AND o.status = 'completed' AND ud.user_id = ?");
$stmt->execute([$user['id'], $user['id']]);
$downloads = $stmt->fetchAll();

// Get user's orders
$stmt = $pdo->prepare("SELECT o.*, p.name as product_name FROM orders o 
                      JOIN products p ON o.product_id = p.id 
                      WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard - Mental Edge Trading</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg: #f3f0ea;
            --bg-soft: #f7f4ee;
            --bg-card: #ffffff;
            --text-main: #1c1c1f;
            --text-muted: #6c6a72;
            --accent: #43523d;
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
        .site-header {
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
        .dashboard-container {
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

        /* Welcome Section */
        .welcome-section {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
        }

        .welcome-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .membership-badge {
            background: var(--accent);
            color: white;
            padding: 6px 12px;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: var(--bg-soft);
            border-radius: var(--radius-md);
            padding: 20px;
            text-align: center;
            border-left: 4px solid var(--accent);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Sections */
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
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Downloads */
        .download-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            margin-bottom: 12px;
        }

        .download-info {
            flex: 1;
        }

        .download-title {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .download-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 106, 16, 0.3);
        }

        .btn.secondary {
            background: var(--text-muted);
        }

        /* Orders Table */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-subtle);
        }

        .orders-table th {
            background: var(--bg-soft);
            font-weight: 600;
            color: var(--text-main);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: var(--radius-pill);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .sidebar {
                position: static;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="index.html" class="logo">
                <i class="fas fa-brain"></i> Mental Edge Trading
            </a>
            <div class="user-info">
                <span class="username">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav>
                <ul class="nav-menu">
                    <li><a href="dashboard.php" class="active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="downloads.php">
                        <i class="fas fa-download"></i> My Downloads
                    </a></li>
                    <li><a href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Order History
                    </a></li>
                    <li><a href="profile.php">
                        <i class="fas fa-user-cog"></i> Profile
                    </a></li>
                    <li><a href="index.html" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Visit Website
                    </a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-header">
                    <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($user['first_name'] ?: $user['username']); ?>!</h1>
                    <div class="membership-badge"><?php echo htmlspecialchars($user['membership_type']); ?> Member</div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($downloads); ?></div>
                        <div class="stat-label">Downloads Available</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($orders); ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo htmlspecialchars($user['membership_type']); ?></div>
                        <div class="stat-label">Membership Type</div>
                    </div>
                </div>
            </div>

            <!-- Downloads Section -->
            <?php if (!empty($downloads)): ?>
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-download"></i> Your Downloads
                    </h2>
                </div>

                <?php foreach ($downloads as $download): ?>
                <div class="download-item">
                    <div class="download-info">
                        <div class="download-title"><?php echo htmlspecialchars($download['name']); ?></div>
                        <div class="download-meta">
                            Size: <?php echo htmlspecialchars($download['file_size']); ?> | 
                            Downloaded: <?php echo $download['download_count']; ?> times | 
                            Last: <?php echo $download['last_download'] ? date('M j, Y', strtotime($download['last_download'])) : 'Never'; ?>
                        </div>
                    </div>
                    <a href="download.php?id=<?php echo $download['id']; ?>" class="btn">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Recent Orders -->
            <?php if (!empty($orders)): ?>
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-shopping-cart"></i> Recent Orders
                    </h2>
                    <a href="orders.php" class="btn secondary">View All</a>
                </div>

                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td>$<?php echo number_format($order['amount'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h2>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <a href="book.html" class="btn" style="text-align: center;">
                        <i class="fas fa-book"></i> Browse Books
                    </a>
                    <a href="blog.html" class="btn secondary" style="text-align: center;">
                        <i class="fas fa-blog"></i> Read Blog
                    </a>
                    <a href="profile.php" class="btn secondary" style="text-align: center;">
                        <i class="fas fa-user-cog"></i> Edit Profile
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>