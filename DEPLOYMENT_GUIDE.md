# Mental Edge Trading - Complete Deployment Guide

## Overview
This guide will walk you through deploying your fully functional Mental Edge Trading website with admin panel, membership system, and e-commerce capabilities on cPanel hosting.

## Prerequisites
- cPanel hosting account with PHP 7.4+ and MySQL 5.7+
- Domain name pointed to your hosting
- Access to cPanel file manager and database tools
- Stripe account (for payments)
- Email service (for password resets)

## Step 1: Database Setup

### 1.1 Create Database
1. Log into cPanel
2. Navigate to "MySQL Databases"
3. Create a new database: `mentaledge_main`
4. Create a database user with strong password
5. Add user to database with ALL PRIVILEGES

### 1.2 Import Database Structure
1. Go to "phpMyAdmin" in cPanel
2. Select your newly created database
3. Click "Import" tab
4. Upload the `setup_database.php` file or run the SQL directly:

```sql
-- Create tables and insert sample data
-- (The complete database structure from setup_database.php)
```

**Alternative**: Upload `setup_database.php` to your web root and access it via browser to auto-create everything.

## Step 2: File Upload

### 2.1 Upload Files
1. Open cPanel "File Manager"
2. Navigate to `public_html` directory
3. Upload all website files maintaining the directory structure:
   ```
   public_html/
   ├── index.html
   ├── about.html
   ├── blog.html
   ├── contact.html
   ├── products.html
   ├── main.js
   ├── style.css
   ├── login.php
   ├── register.php
   ├── dashboard.php
   ├── downloads.php
   ├── payment.php
   ├── forgot_password.php
   ├── reset_password.php
   ├── config.php
   ├── setup_database.php
   └── admin/
       ├── admin_login.php
       ├── admin_dashboard.php
       ├── admin_blog.php
       ├── admin_edit_blog.php
       └── logout.php
   ```

### 2.2 Set File Permissions
- PHP files: 644
- Directories: 755
- config.php: 600 (for security)

## Step 3: Configuration

### 3.1 Update config.php
Edit `/public_html/config.php` with your actual credentials:

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mentaledge_main');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');

// Admin Credentials (Change these!)
define('ADMIN_USERNAME', 'sarahadmin');
define('ADMIN_PASSWORD', '$2y$10$...'); // New hashed password

// Stripe Configuration
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_YOUR_STRIPE_KEY');
define('STRIPE_SECRET_KEY', 'sk_live_YOUR_STRIPE_KEY');

// Email Configuration
define('SMTP_HOST', 'smtp.your-email.com');
define('SMTP_USER', 'your-email@domain.com');
define('SMTP_PASS', 'your-email-password');
define('SMTP_PORT', 587);

define('SITE_URL', 'https://yourdomain.com');
define('SITE_NAME', 'Mental Edge Trading');
?>
```

### 3.2 Generate New Admin Password Hash
Create a simple PHP file to generate a new password hash:

```php
<?php
$password = 'YourNewSecurePassword123!';
echo password_hash($password, PASSWORD_DEFAULT);
?>
```

Upload, run it, then update config.php with the new hash.

## Step 4: Stripe Payment Setup

### 4.1 Stripe Account Setup
1. Create account at stripe.com
2. Complete business verification
3. Get your API keys from Dashboard → Developers → API Keys

### 4.2 Configure Webhooks
1. In Stripe Dashboard, go to Developers → Webhooks
2. Add endpoint: `https://yourdomain.com/payment.php`
3. Select events: `checkout.session.completed`, `payment_intent.succeeded`
4. Copy webhook secret to config.php

### 4.3 Update Payment Products
Edit `payment.php` to match your actual products:

```php
$products = [
    'trading-psychology' => [
        'name' => 'Trading Psychology Mastery',
        'price' => 9900, // $99.00 in cents
        'description' => 'Complete guide to trading psychology'
    ],
    'risk-management' => [
        'name' => 'Advanced Risk Management',
        'price' => 14900,
        'description' => 'Professional risk management strategies'
    ]
];
```

## Step 5: Email Configuration

### 5.1 Email Service Options
**Option A: Use cPanel Email**
1. Create email account in cPanel
2. Use SMTP settings provided

**Option B: Use External SMTP**
- Gmail, SendGrid, or other SMTP service
- Update config.php with correct credentials

### 5.2 Test Email Functionality
Upload this test file to verify email sending:

```php
<?php
require_once 'config.php';
// Test email sending code
?>
```

## Step 6: SSL Certificate

### 6.1 Install SSL
1. In cPanel, go to "SSL/TLS"
2. Install free Let's Encrypt certificate
3. Force HTTPS via .htaccess:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
```

## Step 7: Final Testing

### 7.1 Admin Panel Access
- URL: `https://yourdomain.com/admin/admin_login.php`
- Username: `sarahadmin`
- Password: [Your set password]

### 7.2 Test All Functionality
1. **User Registration**: Create test account
2. **Login System**: Test user and admin login
3. **Blog Management**: Create/edit blog posts
4. **Payment System**: Test purchase flow (use Stripe test mode first)
5. **Password Reset**: Test forgot password functionality
6. **Downloads**: Verify purchased content access
7. **Mobile Responsiveness**: Test on various devices

### 7.3 Security Checklist
- [ ] Change default admin credentials
- [ ] Use strong database password
- [ ] Enable SSL certificate
- [ ] Set correct file permissions
- [ ] Remove setup files after installation
- [ ] Enable security headers in .htaccess
- [ ] Regular backups scheduled

## Step 8: Production Deployment

### 8.1 Switch to Live Mode
1. Update Stripe keys to live mode
2. Test one live transaction
3. Verify email notifications work
4. Check download system functionality

### 8.2 Monitor and Maintain
1. Set up Google Analytics
2. Monitor error logs
3. Regular security updates
4. Backup schedule (weekly)

## Troubleshooting

### Common Issues

**Database Connection Failed**
- Check credentials in config.php
- Verify database user has correct permissions
- Test connection with simple PHP script

**PHP Files Downloading Instead of Executing**
- Check file permissions (should be 644)
- Verify PHP version is 7.4+
- Check .htaccess configuration

**Email Not Sending**
- Verify SMTP credentials
- Check spam folders
- Test with simple mail() function first

**Stripe Payments Not Working**
- Verify API keys are correct
- Check webhook configuration
- Review Stripe dashboard for errors

**Admin Panel Not Accessible**
- Check admin credentials in config.php
- Verify session save path is writable
- Check for PHP errors in logs

### Support Resources
- cPanel Documentation
- PHP Manual
- Stripe Documentation
- MySQL Documentation

## Next Steps
1. Customize content and branding
2. Add more products
3. Implement additional features
4. Monitor performance and optimize
5. Regular security audits

## Contact Information
For technical support or questions about this deployment, refer to your hosting provider's documentation or contact their support team.

---

**Important Security Notes:**
- Always use strong, unique passwords
- Keep software and dependencies updated
- Regularly backup your database and files
- Monitor for suspicious activity
- Use HTTPS for all pages
- Implement rate limiting for login attempts