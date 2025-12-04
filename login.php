<?php
require_once 'config.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Verify user credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = true");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Update last login
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Set session
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_membership'] = $user['membership_type'];
        
        header('Location: dashboard.php');
        exit();
    } else {
        $login_error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login - Mental Edge Trading</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-header {
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 0.9rem;
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

        .btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: var(--radius-pill);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(102, 106, 16, 0.3);
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            margin-bottom: 20px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 0.85rem;
        }

        .form-links a {
            color: var(--accent);
            text-decoration: none;
            transition: color var(--transition-fast);
        }

        .form-links a:hover {
            text-decoration: underline;
        }

        .back-link {
            margin-top: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color var(--transition-fast);
        }

        .back-link:hover {
            color: var(--accent);
        }

        .demo-info {
            background: var(--bg-soft);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .demo-info h3 {
            color: var(--accent);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-user"></i> Member Login</h1>
            <p>Access your Mental Edge Trading account</p>
        </div>

        <?php if ($login_error): ?>
            <div class="error-message show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($login_error); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="form-links">
                <a href="register.php">Create Account</a>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </form>

        <div class="demo-info">
            <h3>Demo Access</h3>
            <p><strong>Username:</strong> TESTLOGIN<br>
            <strong>Password:</strong> USERTEST</p>
        </div>

        <a href="index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Website
        </a>
    </div>
</body>
</html>