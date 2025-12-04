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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Downloads - Mental Edge Trading</title>
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

        /* Page Header */
        .page-header {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
            text-align: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: var(--text-muted);
        }

        /* Downloads Section */
        .downloads-section {
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

        /* Download Items */
        .download-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .download-card {
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 20px;
            transition: all var(--transition-fast);
        }

        .download-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .download-image {
            width: 100%;
            height: 200px;
            background: var(--bg-soft);
            border-radius: var(--radius-md);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 3rem;
        }

        .download-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--radius-md);
        }

        .download-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .download-description {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .download-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .download-stats {
            display: flex;
            gap: 12px;
        }

        .download-stat {
            display: flex;
            align-items: center;
            gap: 4px;
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
            width: 100%;
            justify-content: center;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 106, 16, 0.3);
        }

        .btn:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .empty-state p {
            margin-bottom: 20px;
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
            
            .download-grid {
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
                    <li><a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="downloads.php" class="active">
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
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">My Downloads</h1>
                <p class="page-subtitle">Access your purchased books and resources</p>
            </div>

            <!-- Downloads Section -->
            <div class="downloads-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-download"></i> Your Downloads
                    </h2>
                    <span style="color: var(--text-muted); font-size: 0.9rem;">
                        <?php echo count($downloads); ?> items available
                    </span>
                </div>

                <?php if (!empty($downloads)): ?>
                <div class="download-grid">
                    <?php foreach ($downloads as $download): ?>
                    <div class="download-card">
                        <?php if ($download['image_path']): ?>
                        <div class="download-image">
                            <img src="<?php echo htmlspecialchars($download['image_path']); ?>" alt="<?php echo htmlspecialchars($download['name']); ?>">
                        </div>
                        <?php else: ?>
                        <div class="download-image">
                            <i class="fas fa-book"></i>
                        </div>
                        <?php endif; ?>

                        <h3 class="download-title"><?php echo htmlspecialchars($download['name']); ?></h3>
                        
                        <p class="download-description"><?php echo htmlspecialchars($download['description']); ?></p>
                        
                        <div class="download-meta">
                            <div class="download-stats">
                                <span class="download-stat">
                                    <i class="fas fa-file-pdf"></i>
                                    <?php echo htmlspecialchars($download['file_size']); ?>
                                </span>
                                <span class="download-stat">
                                    <i class="fas fa-download"></i>
                                    <?php echo $download['download_count']; ?> times
                                </span>
                            </div>
                            <span class="download-stat">
                                <i class="fas fa-clock"></i>
                                Last: <?php echo $download['last_download'] ? date('M j, Y', strtotime($download['last_download'])) : 'Never'; ?>
                            </span>
                        </div>

                        <a href="download.php?id=<?php echo $download['id']; ?>" class="btn">
                            <i class="fas fa-download"></i>
                            <?php echo $download['download_count'] > 0 ? 'Download Again' : 'Download Now'; ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-download"></i>
                    <h3>No Downloads Available</h3>
                    <p>You haven't purchased any products yet. Visit our store to get access to exclusive trading psychology resources.</p>
                    <a href="book.html" class="btn">
                        <i class="fas fa-shopping-cart"></i> Browse Products
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Download Instructions -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i> Download Instructions
                    </h2>
                </div>
                
                <div style="line-height: 1.7;">
                    <p><strong>How to download your files:</strong></p>
                    <ol style="margin: 16px 0; padding-left: 20px;">
                        <li>Click the "Download" button next to your purchased item</li>
                        <li>The file will begin downloading to your device</li>
                        <li>Check your Downloads folder or specified location</li>
                        <li>Open the PDF file with your preferred PDF reader</li>
                    </ol>
                    
                    <p><strong>Important notes:</strong></p>
                    <ul style="margin: 16px 0; padding-left: 20px;">
                        <li>You can download each file up to 10 times</li>
                        <li>Downloads are tracked for security purposes</li>
                        <li>Files are in PDF format and work on all devices</li>
                        <li>Contact support if you have any download issues</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>