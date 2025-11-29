<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mental_edge_trading');
define('DB_USER', 'root');
define('DB_PASS', '');

// Admin Configuration
// Real admin credentials as requested
define('ADMIN_USERNAME', 'sarahadmin');
define('ADMIN_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // Will be updated with real hash

// User Configuration
// Test user credentials as requested
define('TEST_USER_USERNAME', 'TESTLOGIN');
define('TEST_USER_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // Will be updated with real hash

// Session Configuration
session_start();

// Demo/Live Mode Configuration
if (!isset($_SESSION['demo_mode'])) {
    $_SESSION['demo_mode'] = true; // Default to demo mode
}

// Database Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Redirect if not admin
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin_login.php');
        exit();
    }
}

// Redirect if not user
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Get current user info
function getCurrentUser() {
    if (isUserLoggedIn()) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Get current admin info
function getCurrentAdmin() {
    if (isAdminLoggedIn()) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
?>