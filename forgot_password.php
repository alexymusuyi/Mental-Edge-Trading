<?php
require_once 'config.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email address is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ? AND is_active = true");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Update user with reset token
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            if ($stmt->execute([$reset_token, $reset_expires, $user['id']])) {
                // In a real implementation, send email here
                // For demo, we'll show the reset link
                $reset_link = "reset_password.php?token=" . $reset_token;
                $message = "Password reset instructions have been sent to your email. In a live system, you would receive an email with a reset link. For demo purposes, use this link: <a href='$reset_link'>$reset_link</a>";
            } else {
                $error = 'Failed to process password reset. Please try again.';
            }
        } else {
            // Don't reveal if email exists or not for security
            $message = 'If an account exists with this email, you will receive password reset instructions.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Mental Edge Trading</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .forgot-container {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .forgot-header {
            margin-bottom: 30px;
        }

        .forgot-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .forgot-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .forgot-form {
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

        .message {
            padding: 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            text-align: left;
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

        .form-links {
            text-align: center;
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-header">
            <h1><i class="fas fa-key"></i> Reset Password</h1>
            <p>Enter your email address and we'll send you instructions to reset your password.</p>
        </div>

        <?php if ($message): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
            <form class="forgot-form" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Instructions
                </button>
            </form>
        <?php endif; ?>

        <div class="form-links">
            Remember your password? <a href="login.php">Login here</a>
        </div>

        <a href="index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Website
        </a>
    </div>
</body>
</html>