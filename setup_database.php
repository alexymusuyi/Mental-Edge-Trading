<?php
// Database Setup Script
// Run this file once to set up the database structure

$host = 'localhost';
$dbname = 'mental_edge_trading';
$user = 'root';
$pass = '';

try {
    // Create database connection without selecting database first
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Select the database
    $pdo->exec("USE $dbname");
    
    // Create admin_users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )");
    
    // Create blog_posts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        content TEXT NOT NULL,
        excerpt TEXT,
        featured_image VARCHAR(500),
        category VARCHAR(100),
        tags VARCHAR(500),
        author VARCHAR(100) DEFAULT 'Sarah Banwart',
        status ENUM('draft', 'published') DEFAULT 'draft',
        published_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        views INT DEFAULT 0,
        meta_title VARCHAR(255),
        meta_description TEXT
    )");
    
    // Create site_analytics table
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_analytics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE UNIQUE NOT NULL,
        page_views INT DEFAULT 0,
        unique_visitors INT DEFAULT 0,
        blog_views INT DEFAULT 0,
        contact_forms INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert default admin user (password: 'admin123')
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password_hash) VALUES (?, ?)");
    $stmt->execute(['admin', $defaultPassword]);
    
    echo "Database setup completed successfully!<br>";
    echo "Default admin login:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br>Please delete this file after setup for security reasons.";
    
} catch(PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>