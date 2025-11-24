# Mental Edge Trading - Complete Testing Guide

## Overview
This comprehensive testing guide will help you verify that all functionality of your Mental Edge Trading website is working correctly before and after deployment.

## Pre-Deployment Testing Checklist

### 1. Local Environment Setup
- [ ] PHP 7.4+ installed and configured
- [ ] MySQL/MariaDB database running
- [ ] Apache/Nginx web server configured
- [ ] SSL certificate for HTTPS (can use self-signed for testing)
- [ ] Error reporting enabled in PHP

### 2. Database Testing
```sql
-- Test database connection
-- Run this in your database management tool

-- Check tables exist
SHOW TABLES;

-- Verify admin user exists
SELECT * FROM admin_users;

-- Test sample blog posts
SELECT * FROM blog_posts WHERE status = 'published';

-- Check products are loaded
SELECT * FROM products WHERE is_active = TRUE;

-- Verify analytics data
SELECT * FROM analytics ORDER BY date DESC LIMIT 5;
```

### 3. Configuration Testing
Create `test_config.php`:

```php
<?php
require_once 'config.php';

echo "=== Configuration Test ===\n";
echo "Database Host: " . DB_HOST . "\n";
echo "Database Name: " . DB_NAME . "\n";
echo "Site URL: " . SITE_URL . "\n";
echo "Stripe Mode: " . STRIPE_MODE . "\n";
echo "From Email: " . FROM_EMAIL . "\n";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    echo "✓ Database connection successful\n";
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}
?>
```

## Functionality Testing

### 1. User Authentication System

#### Registration Testing
```bash
# Test URL: http://localhost/register.php
```

**Test Cases:**
- [ ] Valid registration (all fields correct)
- [ ] Duplicate username/email
- [ ] Weak password
- [ ] Invalid email format
- [ ] Missing required fields
- [ ] SQL injection attempts in fields

**Expected Results:**
- [ ] Success message and redirect to login
- [ ] User created in database
- [ ] Welcome email sent (if configured)
- [ ] Proper error messages for invalid inputs

#### Login Testing
```bash
# Test URL: http://localhost/login.php
```

**Test Cases:**
- [ ] Valid user credentials
- [ ] Invalid username
- [ ] Invalid password
- [ ] Empty fields
- [ ] Multiple failed attempts
- [ ] Remember me functionality

**Expected Results:**
- [ ] Successful login redirects to dashboard
- [ ] Session created properly
- [ ] Failed login shows appropriate error
- [ ] Account lockout after multiple failures

#### Password Reset Testing
```bash
# Test URL: http://localhost/forgot_password.php
```

**Test Cases:**
- [ ] Valid email address
- [ ] Invalid/non-existent email
- [ ] Expired reset token
- [ ] Valid reset token
- [ ] Password mismatch during reset

**Expected Results:**
- [ ] Reset email sent successfully
- [ ] Token stored in database
- [ ] Password updated successfully
- [ ] Token invalidated after use

### 2. Admin Panel Testing

#### Admin Login
```bash
# Test URL: http://localhost/admin/admin_login.php
```

**Test Cases:**
- [ ] Valid admin credentials
- [ ] Regular user trying to access admin
- [ ] Invalid admin credentials
- [ ] Direct access to admin pages without login

**Expected Results:**
- [ ] Admin access granted
- [ ] Regular users redirected
- [ ] Session management works correctly

#### Admin Dashboard
```bash
# Test URL: http://localhost/admin/admin_dashboard.php
```

**Test Cases:**
- [ ] Statistics display correctly
- [ ] Charts load properly
- [ ] Recent blog posts show
- [ ] Analytics data updates

**Expected Results:**
- [ ] All metrics display real data
- [ ] Charts render correctly
- [ ] No PHP errors in logs

#### Blog Management
```bash
# Test URL: http://localhost/admin/admin_blog.php
```

**Test Cases:**
- [ ] Create new blog post
- [ ] Edit existing blog post
- [ ] Delete blog post
- [ ] Upload images
- [ ] Set post status (draft/published)
- [ ] Add categories and tags

**Expected Results:**
- [ ] Posts saved to database
- [ ] Changes reflect immediately
- [ ] Images upload successfully
- [ ] Proper validation messages

### 3. Blog System Testing

#### Blog Display
```bash
# Test URL: http://localhost/blog.php
```

**Test Cases:**
- [ ] Blog posts display correctly
- [ ] Pagination works
- [ ] Individual post pages load
- [ ] Search functionality
- [ ] Category filtering
- [ ] Reading time calculation

**Expected Results:**
- [ ] All published posts visible
- [ ] Post content renders properly
- [ ] Images display correctly
- [ ] Navigation between posts works

### 4. Payment System Testing

#### Test Mode Setup
```php
// Ensure test mode is enabled in config.php
define('STRIPE_MODE', 'test');
```

#### Payment Flow Testing
```bash
# Test URL: http://localhost/payment.php
```

**Test Cases:**
- [ ] Product selection
- [ ] Payment form loads
- [ ] Test card: 4242 4242 4242 4242
- [ ] Declined card: 4000 0000 0000 0002
- [ ] 3D Secure card: 4000 0000 0000 3220
- [ ] Invalid card details
- [ ] Network error simulation

**Expected Results:**
- [ ] Payment processed successfully
- [ ] Order recorded in database
- [ ] Confirmation email sent
- [ ] User can access downloads

#### Webhook Testing
```bash
# Use Stripe CLI for webhook testing
stripe login
stripe listen --forward-to localhost/payment.php
```

### 5. Download System Testing

#### Access Control
```bash
# Test URL: http://localhost/downloads.php
```

**Test Cases:**
- [ ] User with valid purchase
- [ ] User without purchase
- [ ] Expired download link
- [ ] Download limit exceeded
- [ ] Direct file access attempts

**Expected Results:**
- [ ] Only authorized users can download
- [ ] Download count tracked
- [ ] Expired links rejected
- [ ] Files served correctly

### 6. Email System Testing

#### Email Functionality
Create `test_email.php`:

```php
<?php
require_once 'config.php';
require_once 'email_functions.php';

echo "=== Email Testing ===\n";

// Test welcome email
$result1 = sendWelcomeEmail('test@example.com', 'TestUser');
echo "Welcome email: " . ($result1 ? "✓ Sent" : "✗ Failed") . "\n";

// Test password reset
$result2 = sendPasswordResetEmail('test@example.com', 'test-token-123');
echo "Password reset email: " . ($result2 ? "✓ Sent" : "✗ Failed") . "\n";

// Test order confirmation
$orderDetails = [
    'order_number' => 'ORD-12345',
    'product_name' => 'Test Product',
    'amount' => '99.00'
];
$result3 = sendOrderConfirmationEmail('test@example.com', $orderDetails);
echo "Order confirmation email: " . ($result3 ? "✓ Sent" : "✗ Failed") . "\n";
?>
```

**Test Cases:**
- [ ] Welcome email on registration
- [ ] Password reset email
- [ ] Order confirmation email
- [ ] Email delivery to major providers
- [ ] Email template rendering

**Expected Results:**
- [ ] All emails sent successfully
- [ ] No spam folder delivery
- [ ] Proper email formatting

### 7. Security Testing

#### SQL Injection Testing
```bash
# Test in registration form
Username: admin' OR '1'='1
Email: test@test.com'
Password: password123
```

**Expected Results:**
- [ ] Input sanitized properly
- [ ] No database errors
- [ ] Prepared statements working

#### XSS Testing
```bash
# Test in blog comments or contact form
<script>alert('XSS')</script>
<img src=x onerror=alert('XSS')>
```

**Expected Results:**
- [ ] Scripts not executed
- [ ] HTML entities encoded
- [ ] No security vulnerabilities

#### CSRF Testing
```bash
# Test form submissions from external sites
```

**Expected Results:**
- [ ] CSRF tokens validated
- [ ] External submissions rejected

### 8. Performance Testing

#### Load Testing
Use Apache Bench or similar tools:

```bash
# Test home page
ab -n 100 -c 10 http://localhost/index.html

# Test database-heavy pages
ab -n 50 -c 5 http://localhost/blog.php

# Test login system
ab -n 20 -c 2 -p login_data.txt -T application/x-www-form-urlencoded http://localhost/login.php
```

**Expected Results:**
- [ ] Page load times under 3 seconds
- [ ] No database connection errors
- [ ] Memory usage within limits

#### Database Performance
```sql
-- Check slow queries
SHOW PROCESSLIST;

-- Analyze table performance
EXPLAIN SELECT * FROM blog_posts WHERE status = 'published';

-- Check index usage
SHOW INDEX FROM users;
```

### 9. Mobile Responsiveness Testing

#### Device Testing
Test on various devices:
- [ ] iPhone (Safari)
- [ ] Android phone (Chrome)
- [ ] iPad (Safari)
- [ ] Android tablet (Chrome)
- [ ] Desktop (Chrome, Firefox, Safari, Edge)

#### Viewport Testing
```bash
# Use browser developer tools
# Test responsive breakpoints: 320px, 768px, 1024px, 1440px
```

**Expected Results:**
- [ ] Navigation works on all devices
- [ ] Forms are usable on mobile
- [ ] Text is readable without zooming
- [ ] Touch targets are appropriate size

## Post-Deployment Testing

### 1. Production Environment Verification

#### SSL Certificate Check
```bash
# Test SSL configuration
openssl s_client -connect yourdomain.com:443

# Check certificate validity
curl -I https://yourdomain.com
```

#### DNS Configuration
```bash
# Verify DNS records
dig yourdomain.com
dig www.yourdomain.com

# Check mail server records
dig MX yourdomain.com
```

### 2. Live Payment Testing

#### Small Transaction Test
1. Create a test product for $1.00
2. Make a real purchase with your card
3. Verify:
   - Payment processes successfully
   - Order appears in Stripe Dashboard
   - Confirmation email received
   - Download access works

#### Refund Test
1. Process a small refund through Stripe
2. Verify user receives notification
3. Check download access is revoked (if applicable)

### 3. Email Deliverability Testing

#### Email Tests
Send test emails to:
- [ ] Gmail account
- [ ] Outlook/Hotmail account
- [ ] Yahoo account
- [ ] Corporate email account

**Check for:**
- [ ] Email delivery to inbox (not spam)
- [ ] Proper formatting
- [ ] Working links
- [ ] Images displaying correctly

### 4. Security Verification

#### Security Headers Check
```bash
# Test security headers
curl -I -X HEAD https://yourdomain.com

# Check for:
# - Strict-Transport-Security
# - X-Content-Type-Options
# - X-Frame-Options
# - X-XSS-Protection
```

#### File Permissions Check
```bash
# Check file permissions on server
find /path/to/website -type f -name "*.php" -exec ls -l {} \;

# Verify config.php has restricted permissions
ls -la config.php
# Should show: -rw------- or -rw-r--r--
```

## Common Issues and Solutions

### Database Connection Issues
```php
// Add to config.php for debugging
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
```

### Email Sending Problems
```php
// Enable PHPMailer debug mode
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';
```

### Stripe Webhook Issues
```php
// Log webhook events
file_put_contents('webhook.log', date('Y-m-d H:i:s') . ' - ' . json_encode($event) . "\n", FILE_APPEND);
```

### Performance Issues
```php
// Add to pages for debugging
$start_time = microtime(true);
// ... page code ...
$end_time = microtime(true);
echo "Page generated in " . ($end_time - $start_time) . " seconds";
```

## Final Deployment Checklist

### Pre-Launch
- [ ] All functionality tested locally
- [ ] Database schema created on production
- [ ] Configuration updated for production
- [ ] SSL certificate installed
- [ ] Email service configured
- [ ] Stripe keys updated to live mode
- [ ] Security headers configured
- [ ] File permissions set correctly
- [ ] Error reporting disabled
- [ ] Backups scheduled

### Launch Day
- [ ] Upload all files to production
- [ ] Import database structure
- [ ] Test admin login
- [ ] Test user registration
- [ ] Test payment flow with small amount
- [ ] Verify email functionality
- [ ] Check mobile responsiveness
- [ ] Monitor error logs
- [ ] Test contact forms
- [ ] Verify download system

### Post-Launch
- [ ] Monitor site performance
- [ ] Check error logs regularly
- [ ] Update software as needed
- [ ] Backup verification
- [ ] Security monitoring
- [ ] User feedback collection

## Emergency Procedures

### If Site Goes Down
1. Check server status with hosting provider
2. Review recent changes
3. Check error logs
4. Restore from backup if necessary
5. Contact support if needed

### If Database Corrupted
1. Stop website access
2. Restore from latest backup
3. Verify data integrity
4. Test functionality
5. Resume service

### If Security Breach
1. Change all passwords immediately
2. Check access logs
3. Update all software
4. Scan for malware
5. Notify users if data compromised

## Support Resources

### Technical Support
- **cPanel Support**: Contact your hosting provider
- **PHP Documentation**: https://php.net/manual/
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **Stripe Support**: https://support.stripe.com/

### Community Help
- **Stack Overflow**: PHP, MySQL, Stripe tags
- **Reddit**: r/webdev, r/PHP
- **Stripe Community**: https://stripe.com/docs/community

---

**Remember:** Always test thoroughly before going live. Keep regular backups and monitor your site performance. Document any customizations you make for future reference.