-- Mental Edge Trading Database Schema
-- Complete database structure with sample data

CREATE DATABASE IF NOT EXISTS mentaledge_main;
USE mentaledge_main;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Regular Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    membership_type ENUM('free', 'premium', 'vip') DEFAULT 'free',
    membership_expires DATE NULL,
    reset_token VARCHAR(255) NULL,
    reset_expires TIMESTAMP NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts Table
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(500),
    author_id INT,
    category VARCHAR(100),
    tags TEXT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    view_count INT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    file_path VARCHAR(500),
    file_size INT,
    file_type VARCHAR(50),
    image_url VARCHAR(500),
    category VARCHAR(100),
    tags TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    download_limit INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    stripe_payment_id VARCHAR(255),
    stripe_customer_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    billing_name VARCHAR(100),
    billing_email VARCHAR(100),
    download_count INT DEFAULT 0,
    last_download TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Downloads Table
CREATE TABLE IF NOT EXISTS user_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    download_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Analytics Table
CREATE TABLE IF NOT EXISTS analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE UNIQUE NOT NULL,
    page_views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    blog_views INT DEFAULT 0,
    product_views INT DEFAULT 0,
    new_users INT DEFAULT 0,
    orders_count INT DEFAULT 0,
    revenue DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User
-- Password: SarahAdmin7722 (change this in production!)
INSERT INTO admin_users (username, password_hash, email) VALUES 
('sarahadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@mentaledge.com');

-- Insert Sample Blog Posts
INSERT INTO blog_posts (title, slug, content, excerpt, category, tags, status, published_at, author_id) VALUES
('Mastering Trading Psychology: The Key to Consistent Profits', 'mastering-trading-psychology', 
'<h2>The Foundation of Trading Success</h2>
<p>Trading psychology is often the most overlooked aspect of successful trading, yet it accounts for over 80% of trading performance. Many traders focus solely on technical analysis and market fundamentals, completely ignoring the mental game that separates profitable traders from those who consistently lose money.</p>

<h3>The Four Pillars of Trading Psychology</h3>
<p><strong>1. Emotional Control:</strong> The ability to maintain composure during market volatility and stick to your trading plan regardless of emotional impulses.</p>
<p><strong>2. Risk Management:</strong> Understanding that preserving capital is more important than chasing profits.</p>
<p><strong>3. Discipline:</strong> Following your trading rules consistently, even when it feels uncomfortable.</p>
<p><strong>4. Patience:</strong> Waiting for the right setups and avoiding the temptation to overtrade.</p>

<h3>Common Psychological Traps</h3>
<p>Fear of missing out (FOMO), revenge trading, and analysis paralysis are just a few of the psychological barriers that prevent traders from achieving consistent profitability. Learning to identify and overcome these mental obstacles is crucial for long-term success.</p>

<h3>Building Mental Resilience</h3>
<p>Developing mental resilience requires consistent practice, self-reflection, and often working with a trading coach or mentor. Keeping a detailed trading journal helps identify patterns in your decision-making process and emotional responses to different market conditions.</p>', 
'Discover the psychological principles that separate successful traders from those who consistently struggle in the markets.', 
'Trading Psychology', 'psychology,emotions,discipline,risk management', 'published', NOW(), 1),

('Advanced Risk Management Strategies for Professional Traders', 'advanced-risk-management', 
'<h2>Beyond Basic Risk Management</h2>
<p>While most traders understand the importance of setting stop losses, professional traders employ sophisticated risk management techniques that go far beyond basic position sizing. These advanced strategies can mean the difference between steady growth and catastrophic losses.</p>

<h3>The Kelly Criterion in Trading</h3>
<p>The Kelly Criterion helps determine the optimal position size based on your win rate and average win/loss ratio. However, many professional traders use a fraction of the full Kelly percentage to account for estimation errors and market uncertainty.</p>

<h3>Portfolio Heat Mapping</h3>
<p>Understanding correlation between positions is crucial. Professional traders use heat mapping to visualize risk concentration and ensure they are not overexposed to similar market movements across different positions.</p>

<h3>Dynamic Position Sizing</h3>
<p>Risk management should adapt to changing market conditions. During high volatility periods, reducing position sizes can help preserve capital. Conversely, when market conditions are favorable, calculated increases in exposure can maximize returns.</p>

<h3>The Psychology of Risk</h3>
<p>Perhaps the most important aspect of risk management is understanding your own risk tolerance and emotional response to losses. Professional traders know that managing emotions is just as important as managing mathematical risk.</p>', 
'Learn advanced risk management techniques used by professional traders to protect capital and maximize returns.', 
'Risk Management', 'risk management,position sizing,Kelly criterion,portfolio management', 'published', NOW(), 1);

-- Insert Sample Products
INSERT INTO products (name, slug, description, price, file_path, file_size, file_type, image_url, category, download_limit) VALUES
('Trading Psychology Mastery Guide', 'trading-psychology-guide', 
'Complete comprehensive guide to mastering trading psychology. Includes practical exercises, case studies, and proven strategies for developing the mental edge needed for consistent trading success. This 150-page PDF covers everything from basic psychological principles to advanced mental training techniques used by professional traders.', 
99.00, 'downloads/trading-psychology-mastery.pdf', 5242880, 'application/pdf', 'images/products/trading-psychology-guide.jpg', 'Trading Psychology', 10),

('Advanced Risk Management Strategies', 'risk-management-strategies', 
'Professional-level risk management course including position sizing calculators, correlation matrices, and advanced portfolio management techniques. Features real-world case studies from successful hedge fund managers and proprietary trading firms. Includes Excel templates and video tutorials.', 
149.00, 'downloads/risk-management-strategies.pdf', 8912896, 'application/pdf', 'images/products/risk-management-strategies.jpg', 'Risk Management', 10),

('Market Analysis Framework', 'market-analysis-framework', 
'Complete framework for conducting thorough market analysis including fundamental, technical, and sentiment analysis methodologies. Features proprietary indicators, checklists, and decision-making frameworks used by institutional traders. Includes access to monthly market analysis webinars.', 
199.00, 'downloads/market-analysis-framework.pdf', 10485760, 'application/pdf', 'images/products/market-analysis-framework.jpg', 'Market Analysis', 15);

-- Insert Sample Analytics Data
INSERT INTO analytics (date, page_views, unique_visitors, blog_views, product_views, new_users, orders_count, revenue) VALUES
(DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1250, 380, 420, 180, 25, 3, 447.00),
(DATE_SUB(CURDATE(), INTERVAL 6 DAY), 1380, 420, 450, 210, 32, 5, 745.00),
(DATE_SUB(CURDATE(), INTERVAL 5 DAY), 1520, 465, 510, 240, 28, 4, 596.00),
(DATE_SUB(CURDATE(), INTERVAL 4 DAY), 1680, 520, 580, 280, 35, 6, 894.00),
(DATE_SUB(CURDATE(), INTERVAL 3 DAY), 1450, 445, 490, 220, 22, 3, 447.00),
(DATE_SUB(CURDATE(), INTERVAL 2 DAY), 1720, 580, 620, 320, 42, 8, 1192.00),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 1890, 635, 680, 350, 38, 7, 1043.00);

-- Create Indexes for Better Performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_blog_posts_status ON blog_posts(status);
CREATE INDEX idx_blog_posts_published ON blog_posts(published_at);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_analytics_date ON analytics(date);