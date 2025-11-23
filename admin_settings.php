<?php
require_once 'config.php';
requireAdminLogin();

$message = '';
$error = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($new_password === $confirm_password) {
        // Verify current password
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($current_password, $admin['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
            if ($stmt->execute([$new_hash, $_SESSION['admin_id']])) {
                $message = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password.';
            }
        } else {
            $error = 'Current password is incorrect.';
        }
    } else {
        $error = 'New passwords do not match.';
    }
}

// Get admin info
$stmt = $pdo->prepare("SELECT username, created_at, last_login FROM admin_users WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin_info = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Mental Edge Trading Admin</title>
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

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: var(--bg-soft);
            border-radius: var(--radius-md);
            padding: 20px;
            border-left: 4px solid var(--accent);
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-main);
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            background: var(--bg-soft);
            transition: border-color var(--transition-fast);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
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
                    <li><a href="admin_blog.php">
                        <i class="fas fa-blog"></i> Blog Management
                    </a></li>
                    <li><a href="admin_analytics.php">
                        <i class="fas fa-chart-line"></i> Analytics
                    </a></li>
                    <li><a href="admin_settings.php" class="active">
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

            <!-- Admin Info -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-user-shield"></i> Admin Information
                    </h2>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($admin_info['username']); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Account Created</div>
                        <div class="info-value"><?php echo date('M j, Y', strtotime($admin_info['created_at'])); ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            <?php echo $admin_info['last_login'] ? date('M j, Y H:i', strtotime($admin_info['last_login'])) : 'Never'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-lock"></i> Change Password
                    </h2>
                </div>

                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">
                            <i class="fas fa-save"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Information -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i> System Information
                    </h2>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">PHP Version</div>
                        <div class="info-value"><?php echo PHP_VERSION; ?></div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Database</div>
                        <div class="info-value">MySQL</div>
                    </div>
                    <div class="info-card">
                        <div class="info-label">Server Time</div>
                        <div class="info-value"><?php echo date('M j, Y H:i:s'); ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>