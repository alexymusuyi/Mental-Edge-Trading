<?php
require_once 'config.php';

// Get published blog posts
$stmt = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC");
$posts = $stmt->fetchAll();

// Get categories
$stmt = $pdo->query("SELECT DISTINCT category FROM blog_posts WHERE status = 'published'");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get popular posts
$stmt = $pdo->query("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY views DESC LIMIT 5");
$popular_posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mental Edge Trading – Blog & Weekly Insights</title>
    <meta name="description" content="Expert trading psychology insights from Sarah Banwart, MSW. Learn how to master your mental edge, overcome emotional trading, and build lasting discipline.">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Main styles -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Blog-page specific layout styles (keeps global theme intact) -->
    <style>
      /* Shell & background */
      .blog-page-shell {
        max-width: 1120px;
        margin: 0 auto;
      }

      .blog-category-tabs {
        display: inline-flex;
        gap: 28px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        border-bottom: 1px solid var(--border-subtle);
        padding-bottom: 10px;
        margin-bottom: 10px;
      }

      .blog-category-tabs button {
        background: none;
        border: none;
        padding: 0;
        font: inherit;
        cursor: pointer;
        color: var(--text-muted);
        position: relative;
        transition: color 0.35s ease-out;
      }

      .blog-category-tabs button.is-active {
        color: #666A10;
      }

      .blog-category-tabs button.is-active::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: -10px;
        height: 2px;
        background: #666A10;
      }

      .blog-entry-list {
        margin-top: 26px;
      }

      .blog-entry {
        padding: 24px 0;
        border-top: 1px solid var(--border-subtle);
        transition: opacity 0.35s ease-out, transform 0.35s ease-out;
      }

      .blog-entry:first-of-type {
        border-top: none;
      }

      .blog-entry-link {
        display: block;
        text-decoration: none;
        color: inherit;
      }

      .blog-entry-inner {
        display: grid;
        grid-template-columns: minmax(0, 260px) minmax(0, 1fr);
        gap: 24px;
      }

      .blog-thumb {
        width: 100%;
        padding-bottom: 65%;
        border-radius: 16px;
        background: #000; /* black placeholder image */
        background-size: cover;
        background-position: center;
        position: relative;
        overflow: hidden;
      }

      .blog-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
      }

      .blog-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }

      .blog-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 8px;
        line-height: 1.3;
      }

      .blog-excerpt {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 12px;
      }

      .blog-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
      }

      .blog-tag {
        background: var(--bg-soft);
        color: var(--text-muted);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
      }

      .blog-stats {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 0.85rem;
        color: var(--text-muted);
      }

      .blog-stat {
        display: flex;
        align-items: center;
        gap: 4px;
      }

      /* Blog detail view */
      .blog-detail {
        display: none;
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
      }

      .blog-detail.active {
        display: block;
      }

      .blog-detail-header {
        text-align: center;
        margin-bottom: 40px;
      }

      .blog-detail-meta {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 16px;
      }

      .blog-detail-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
      }

      .blog-detail-excerpt {
        font-size: 1.1rem;
        color: var(--text-muted);
        margin-bottom: 30px;
        line-height: 1.6;
      }

      .blog-detail-content {
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 40px;
      }

      .blog-detail-content h2 {
        color: var(--accent);
        margin: 30px 0 16px;
        font-size: 1.5rem;
      }

      .blog-detail-content h3 {
        color: var(--accent);
        margin: 24px 0 12px;
        font-size: 1.2rem;
      }

      .blog-detail-content p {
        margin-bottom: 16px;
      }

      .blog-detail-content ul, .blog-detail-content ol {
        margin: 16px 0;
        padding-left: 24px;
      }

      .blog-detail-content li {
        margin-bottom: 8px;
      }

      .blog-detail-content blockquote {
        border-left: 4px solid var(--accent);
        padding-left: 20px;
        margin: 24px 0;
        font-style: italic;
        color: var(--text-muted);
      }

      .blog-detail-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 30px;
        border-top: 1px solid var(--border-subtle);
      }

      .back-to-list {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .back-to-list:hover {
        text-decoration: underline;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .blog-entry-inner {
          grid-template-columns: 1fr;
          gap: 16px;
        }
        
        .blog-thumb {
          padding-bottom: 50%;
        }
        
        .blog-detail-title {
          font-size: 1.8rem;
        }
      }
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <header class="site-header">
        <nav class="nav">
            <div class="nav-inner">
                <a href="index.html#top" class="nav-logo">
                    <img src="assets/img/MET-Logo.png" alt="Mental Edge Trading logo">
                    <span class="nav-logo-text">Mental Edge Trading</span>
                </a>

                <button class="nav-toggle" id="navToggle" aria-label="Open navigation">
                    <span></span><span></span><span></span>
                </button>

                <ul class="nav-links" id="navLinks">
                    <li><a href="podcast.html">Podcast</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="blog.php" class="active">Blog</a></li>
                    <li><a href="book.html">Book &amp; Workbook</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="login.php" class="login-btn"><i class="fas fa-lock"></i> Member Login</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <!-- PAGE HEADER -->
        <section class="section page-hero reveal">
            <div class="page-hero-inner">
                <h1>Mental Edge Blog</h1>
                <p class="hero-subtitle">
                    Weekly insights on trading psychology, emotional discipline, and building the mental edge that separates successful traders from the rest.
                </p>
            </div>
        </section>

        <!-- BLOG CONTENT -->
        <section class="section">
            <div class="blog-page-shell">
                <!-- Blog List View -->
                <div id="blogList" class="blog-list-view">
                    <!-- Category Filter -->
                    <div class="blog-category-tabs">
                        <button class="is-active" data-category="all">All Posts</button>
                        <?php foreach ($categories as $category): ?>
                        <button data-category="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Blog Entries -->
                    <div class="blog-entry-list">
                        <?php foreach ($posts as $post): ?>
                        <article class="blog-entry" data-category="<?php echo htmlspecialchars($post['category']); ?>">
                            <a href="#" class="blog-entry-link" onclick="showBlogDetail(<?php echo $post['id']; ?>)" data-post-id="<?php echo $post['id']; ?>">
                                <div class="blog-entry-inner">
                                    <?php if ($post['featured_image']): ?>
                                    <div class="blog-thumb" style="background-image: url('<?php echo htmlspecialchars($post['featured_image']); ?>')"></div>
                                    <?php else: ?>
                                    <div class="blog-thumb" style="background: linear-gradient(135deg, #666A10, #8b8b8b)"></div>
                                    <?php endif; ?>
                                    
                                    <div class="blog-content">
                                        <div class="blog-meta">
                                            <?php echo htmlspecialchars($post['category']); ?> • <?php echo date('M j, Y', strtotime($post['published_at'])); ?>
                                        </div>
                                        
                                        <h2 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                                        
                                        <p class="blog-excerpt"><?php echo htmlspecialchars($post['excerpt'] ?: substr($post['content'], 0, 150) . '...'); ?></p>
                                        
                                        <?php if ($post['tags']): ?>
                                        <div class="blog-tags">
                                            <?php foreach (explode(',', $post['tags']) as $tag): ?>
                                            <span class="blog-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="blog-stats">
                                            <span class="blog-stat">
                                                <i class="fas fa-eye"></i>
                                                <?php echo number_format($post['views']); ?> views
                                            </span>
                                            <span class="blog-stat">
                                                <i class="fas fa-clock"></i>
                                                <?php echo reading_time($post['content']); ?> min read
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Blog Detail View -->
                <div id="blogDetail" class="blog-detail">
                    <div class="blog-detail-header">
                        <div class="blog-detail-meta" id="detailMeta"></div>
                        <h1 class="blog-detail-title" id="detailTitle"></h1>
                        <p class="blog-detail-excerpt" id="detailExcerpt"></p>
                    </div>
                    
                    <div class="blog-detail-content" id="detailContent"></div>
                    
                    <div class="blog-detail-footer">
                        <a href="#" class="back-to-list" onclick="showBlogList()">
                            <i class="fas fa-arrow-left"></i> Back to Blog
                        </a>
                        <div class="blog-stats" id="detailStats"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="footer-inner">
            <p>&copy; 2024 Mental Edge Trading. All rights reserved.</p>
            <p>Helping traders master their mental edge through evidence-based psychology.</p>
        </div>
    </footer>

    <script>
        // Blog functionality
        function showBlogDetail(postId) {
            // Hide list view, show detail view
            document.getElementById('blogList').style.display = 'none';
            document.getElementById('blogDetail').classList.add('active');
            
            // Get post data
            const postElement = document.querySelector(`[data-post-id="${postId}"]`);
            if (postElement) {
                const title = postElement.querySelector('.blog-title').textContent;
                const meta = postElement.querySelector('.blog-meta').textContent;
                const excerpt = postElement.querySelector('.blog-excerpt').textContent;
                
                // Update detail view (in a real implementation, you'd fetch the full content)
                document.getElementById('detailTitle').textContent = title;
                document.getElementById('detailMeta').textContent = meta;
                document.getElementById('detailExcerpt').textContent = excerpt;
                document.getElementById('detailContent').innerHTML = '<p>Full blog content would be loaded here...</p>';
                
                // Update stats
                const stats = postElement.querySelector('.blog-stats').innerHTML;
                document.getElementById('detailStats').innerHTML = stats;
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function showBlogList() {
            document.getElementById('blogList').style.display = 'block';
            document.getElementById('blogDetail').classList.remove('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Category filtering
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.blog-category-tabs button');
            const blogEntries = document.querySelectorAll('.blog-entry');

            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Update active button
                    categoryButtons.forEach(btn => btn.classList.remove('is-active'));
                    this.classList.add('is-active');

                    const category = this.dataset.category;
                    
                    // Filter posts
                    blogEntries.forEach(entry => {
                        if (category === 'all' || entry.dataset.category === category) {
                            entry.style.display = 'block';
                        } else {
                            entry.style.display = 'none';
                        }
                    });
                });
            });
        });

        // Reading time calculation
        function reading_time(content) {
            const wordsPerMinute = 200;
            const words = content.split(' ').length;
            return Math.ceil(words / wordsPerMinute);
        }
    </script>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>

<?php
function reading_time($content) {
    $wordsPerMinute = 200;
    $words = str_word_count(strip_tags($content));
    return max(1, ceil($words / $wordsPerMinute));
}
?>