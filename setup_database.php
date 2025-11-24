<?php
// Database Setup Script - Run this file once to set up the database structure

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
    
    // Create users table for membership
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        membership_type ENUM('free', 'premium', 'vip') DEFAULT 'free',
        membership_expires DATE NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT true,
        reset_token VARCHAR(255) NULL,
        reset_expires TIMESTAMP NULL
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
        author_id INT,
        status ENUM('draft', 'published') DEFAULT 'draft',
        published_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        views INT DEFAULT 0,
        meta_title VARCHAR(255),
        meta_description TEXT,
        FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
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
    
    // Create products table for books/pdfs
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        file_path VARCHAR(500),
        file_size VARCHAR(50),
        image_path VARCHAR(500),
        category VARCHAR(100),
        is_active BOOLEAN DEFAULT true,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        stripe_payment_id VARCHAR(255),
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
    
    // Create user_downloads table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_downloads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        download_count INT DEFAULT 0,
        last_download TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");
    
    // Insert default admin user (sarahadmin / SarahAdmin7722)
    $adminPassword = password_hash('SarahAdmin7722', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password_hash) VALUES (?, ?)");
    $stmt->execute(['sarahadmin', $adminPassword]);
    
    // Insert test user (TESTLOGIN / USERTEST)
    $testPassword = password_hash('USERTEST', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password_hash, membership_type) VALUES (?, ?, ?, ?)");
    $stmt->execute(['TESTLOGIN', 'test@example.com', $testPassword, 'premium']);
    
    // Insert sample blog posts
    $stmt = $pdo->prepare("INSERT IGNORE INTO blog_posts (title, slug, content, excerpt, category, tags, status, published_at) VALUES 
    (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Sample blog post 1
    $content1 = "Revenge trading is one of the most destructive patterns that traders face. It's that overwhelming urge to jump back into the market immediately after a loss, often with larger position sizes, in an attempt to 'get back' at the market...\n\n## Understanding Revenge Trading\n\nRevenge trading stems from our natural human response to loss and perceived injustice. When we lose money in the markets, our brain's threat response system activates, triggering emotional responses that can override rational decision-making.\n\n### The Psychology Behind It\n\n1. **Loss Aversion**: Humans are wired to feel losses more intensely than gains\n2. **Ego Protection**: We want to prove the market wrong\n3. **Emotional Hijacking**: Strong emotions override logical thinking\n4. **Impatience**: Desire for immediate recovery of losses\n\n## Strategies to Overcome Revenge Trading\n\n### 1. Implement a Trading Cool-Off Period\n- Wait 30 minutes after any loss before making another trade\n- Use this time to analyze what went wrong objectively\n- Write down your emotional state and trading plan\n\n### 2. Develop a Pre-Trade Checklist\n- Am I following my trading plan?\n- Is this setup meeting all my criteria?\n- What is my risk-reward ratio?\n- Am I emotionally neutral?\n\n### 3. Use Position Sizing Rules\n- Never increase position size after a loss\n- Consider reducing size during drawdown periods\n- Stick to your predetermined risk per trade\n\n### 4. Practice Mindfulness\n- Develop awareness of your emotional state\n- Use breathing techniques to maintain calm\n- Accept losses as part of the trading business\n\n## Building Long-Term Discipline\n\nThe key to overcoming revenge trading is developing rock-solid discipline. This comes from:\n\n- **Consistent Practice**: Following your rules every single time\n- **Journaling**: Tracking your emotions and decisions\n- **Education**: Continuously learning about trading psychology\n- **Support**: Having a trading community or mentor\n\nRemember, the market doesn't care about your feelings. It rewards discipline, patience, and rational decision-making. Every time you resist the urge to revenge trade, you're building the mental muscles that lead to long-term trading success.\n\n## Conclusion\n\nOvercoming revenge trading isn't about eliminating emotions – it's about managing them effectively. By implementing these strategies and maintaining awareness of your psychological state, you can break the cycle of destructive trading behavior and develop the mental edge needed for consistent profitability.\n\nThe journey from emotional to disciplined trading is challenging but absolutely achievable. Start with one strategy, master it, then add others. Your future trading self will thank you for the work you do today.";
    
    $stmt->execute([
        'How to Stop Revenge Trading',
        'how-to-stop-revenge-trading',
        $content1,
        'Learn proven strategies to overcome revenge trading and develop emotional discipline in your trading practice.',
        'Trading Psychology',
        'trading, psychology, mental edge, discipline',
        'published',
        date('Y-m-d H:i:s', strtotime('-5 days'))
    ]);
    
    // Sample blog post 2
    $content2 = "Fear of Missing Out (FOMO) is one of the most powerful emotions that can derail a trader's success. It's that urgent feeling that you need to enter a trade immediately because you're afraid of missing out on potential profits...\n\n## Understanding FOMO in Trading\n\nFOMO is rooted in our evolutionary psychology. Our brains are wired to avoid missing opportunities, especially when we see others succeeding. In trading, this manifests as:\n\n### Common FOMO Triggers\n\n1. **Seeing Others Profit**: Watching other traders post their wins\n2. **Market Momentum**: Seeing prices move rapidly in one direction\n3. **News Events**: Major announcements that cause market movement\n4. **Social Media**: Trading communities and influencers posting trades\n\n### The Psychology Behind FOMO\n\n- **Social Comparison**: We compare ourselves to other successful traders\n- **Scarcity Mindset**: Fear that opportunities are limited\n- **Impatience**: Wanting immediate results and gratification\n- **Perfectionism**: Feeling like we need to catch every move\n\n## The FOMO Recovery Framework\n\n### Phase 1: Awareness and Recognition\n\n**Step 1: Identify Your FOMO Triggers**
- What situations typically trigger your FOMO?
- Are there specific times of day when it's worse?
- Do certain markets or instruments trigger it more?
\n**Step 2: Track Your FOMO Episodes**
- Keep a FOMO journal documenting when it happens
- Note the emotions and thoughts you experience
- Record the outcomes of FOMO-driven trades\n\n### Phase 2: Prevention Strategies\n\n**1. Develop a Trading Plan**
- Define your exact entry and exit criteria
- Set up alerts for your setups instead of watching constantly
- Have a written plan for different market scenarios\n\n**2. Use Technology to Your Advantage**
- Set price alerts instead of constantly monitoring charts
- Use limit orders to prevent emotional entries
- Consider using trading bots for systematic strategies\n\n**3. Practice Mindfulness**
- Develop awareness of your emotional state while trading
- Use meditation or breathing exercises to maintain calm
- Accept that missing trades is part of the business\n\n### Phase 3: Recovery Techniques\n\n**1. The STOP Technique**
- **S**top what you're doing\n- **T**ake a deep breath\n- **O**bserve your emotions and thoughts\n- **P**roceed with your trading plan\n\n**2. The 24-Hour Rule**
- Wait 24 hours before making any FOMO-driven decisions
- Use this time to analyze the setup objectively\n- Often, the "perfect" trade looks different after time passes\n\n**3. Focus on Process Over Outcome**
- Measure success by following your plan, not by profits\n- Celebrate when you resist FOMO, regardless of market outcome\n- Build confidence in your systematic approach\n\n## Building FOMO Resistance\n\n### Long-Term Strategies\n\n1. **Develop Multiple Strategies**: Have different approaches for different market conditions\n2. **Build a Trading Community**: Surround yourself with disciplined traders\n3. **Focus on Education**: Continuously learn about market dynamics\n4. **Practice Patience**: Remember that there will always be another opportunity\n\n### Daily Practices\n\n1. **Morning Routine**: Start each trading day with mindfulness\n2. **Pre-Market Analysis**: Have your watchlist ready before market open\n3. **Post-Market Review**: Analyze your decisions and emotional state\n4. **Weekend Planning**: Prepare for the week ahead without market pressure\n\n## The Role of Social Media\n\nSocial media can be both helpful and harmful for traders dealing with FOMO:\n\n### Potential Pitfalls\n- Seeing only winning trades from others\n- Comparing your journey to others' highlight reels\n- Feeling pressure to be constantly active\n\n### Healthy Usage\n- Follow traders who emphasize process over profits\n- Use social media for education, not comparison\n- Take regular breaks from trading social media\n\n## Creating Your FOMO Recovery Plan\n\nEvery trader's FOMO experience is unique. Create a personalized recovery plan that includes:\n\n1. **Your Personal FOMO Triggers**
2. **Specific Prevention Strategies**
3. **Emergency Protocols for FOMO Episodes**
4. **Daily and Weekly Routines**
5. **Progress Tracking Methods**
\nRemember, overcoming FOMO is a process, not a one-time fix. Be patient with yourself as you develop new habits and mental frameworks. The goal isn't to eliminate all fear, but to manage it effectively so it doesn't control your trading decisions.\n\nWith consistent practice and the right mindset, you can transform FOMO from a destructive force into a signal that reminds you to stick to your process and trust in your systematic approach to trading.";
    
    $stmt->execute([
        'FOMO Recovery Guide',
        'fomo-recovery-guide',
        $content2,
        'A comprehensive guide to understanding and overcoming Fear of Missing Out in trading.',
        'Trading Psychology',
        'fomo, psychology, trading, emotions',
        'published',
        date('Y-m-d H:i:s', strtotime('-7 days'))
    ]);
    
    // Insert sample product (book)
    $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, description, price, file_path, file_size, image_path, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Mental Edge Trading Book & Workbook Bundle',
        'Complete guide to mastering trading psychology with practical exercises and real-world examples.',
        49.99,
        'assets/downloads/mental-edge-trading-book.pdf',
        '2.5 MB',
        'assets/img/book-cover.jpg',
        'Books'
    ]);
    
    echo "Database setup completed successfully!<br>";
    echo "Admin login:<br>";
    echo "Username: sarahadmin<br>";
    echo "Password: SarahAdmin7722<br><br>";
    echo "Test user login:<br>";
    echo "Username: TESTLOGIN<br>";
    echo "Password: USERTEST<br><br>";
    echo "Please delete this file after setup for security reasons.";
    
} catch(PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>