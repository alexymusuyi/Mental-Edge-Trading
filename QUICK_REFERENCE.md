# Mental Edge Trading - Quick Reference Guide

## Admin Panel Access
- **URL**: `https://yourdomain.com/admin/admin_login.php`
- **Username**: `sarahadmin`
- **Password**: [Set your own secure password]

## Default Credentials (Change Immediately!)
```php
// In config.php - CHANGE THESE!
define('ADMIN_USERNAME', 'sarahadmin');
define('ADMIN_PASSWORD', 'your-secure-password-hash');
```

## Database Connection
```php
// Test database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
```

## Important File Locations
```
/public_html/
├── index.html              # Homepage
├── login.php               # User login
├── register.php            # User registration
├── forgot_password.php     # Password reset
├── dashboard.php           # User dashboard
├── downloads.php           # User downloads
├── payment.php             # Payment processing
├── config.php              # Configuration (SECURE!)
├── main.js                 # JavaScript functionality
├── style.css               # Stylesheet
├── admin/
│   ├── admin_login.php     # Admin login
│   ├── admin_dashboard.php # Admin dashboard
│   ├── admin_blog.php      # Blog management
│   └── admin_edit_blog.php # Edit blog posts
└── downloads/              # Protected download directory
    ├── trading-psychology-mastery.pdf
    ├── risk-management-strategies.pdf
    └── market-analysis-framework.pdf
```

## Essential Configuration
```php
// config.php - Critical settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'mentaledge_main');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

define('STRIPE_PUBLISHABLE_KEY', 'pk_live_YOUR_STRIPE_KEY');
define('STRIPE_SECRET_KEY', 'sk_live_YOUR_STRIPE_KEY');
define('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET');

define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_USER', 'apikey');
define('SMTP_PASS', 'SG.YOUR_SENDGRID_KEY');
define('FROM_EMAIL', 'noreply@yourdomain.com');

define('SITE_URL', 'https://yourdomain.com');
```

## Common Commands

### File Permissions
```bash
# Set correct permissions
chmod 644 *.php
chmod 600 config.php
chmod 755 admin/
chmod 755 downloads/
chmod 644 downloads/*.pdf
```

### Database Backup
```bash
# Backup database
mysqldump -u username -p mentaledge_main > backup_$(date +%Y%m%d).sql

# Restore database
mysql -u username -p mentaledge_main < backup_file.sql
```

### Error Log Check
```bash
# Check PHP errors
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log
```

## Testing Checklist

### Quick Functionality Test
1. **Homepage**: Load `index.html` - check all sections display
2. **User Registration**: Create test account
3. **User Login**: Login with test account
4. **Admin Login**: Access admin panel
5. **Blog Management**: Create/edit a blog post
6. **Payment Test**: Use test card `4242 4242 4242 4242`
7. **Email Test**: Check password reset email
8. **Download Test**: Verify purchased content access

### Security Checklist
- [ ] config.php has 600 permissions
- [ ] Admin password changed from default
- [ ] Database user has limited privileges
- [ ] SSL certificate installed
- [ ] Error reporting disabled in production
- [ ] File upload restrictions in place

### Performance Checklist
- [ ] Page load times under 3 seconds
- [ ] Database indexes created
- [ ] Images optimized
- [ ] JavaScript minified
- [ ] CSS optimized

## Troubleshooting Quick Fixes

### "Cannot connect to database"
```php
// Check these in config.php
1. DB_HOST (usually 'localhost')
2. DB_NAME matches database name
3. DB_USER and DB_PASS are correct
4. User has privileges on database
```

### "Stripe payment failed"
```php
// Check these
1. Stripe keys are correct (test vs live)
2. Webhook URL is correct
3. Product prices are in cents (9900 = $99.00)
4. SSL certificate is valid
```

### "Email not sending"
```php
// Check these
1. SMTP credentials are correct
2. Port 587/465 is open on server
3. FROM_EMAIL is valid
4. SPF/DKIM records configured
```

### "Admin panel not accessible"
```php
// Check these
1. Admin credentials in config.php
2. Session save path is writable
3. No PHP errors in logs
4. Correct admin login URL
```

## Emergency Contacts

### Hosting Support
- **cPanel**: Contact your hosting provider
- **Database**: Check phpMyAdmin or hosting control panel
- **File Manager**: Use cPanel File Manager or FTP

### External Services
- **Stripe**: https://support.stripe.com/
- **SendGrid**: https://support.sendgrid.com/
- **Domain**: Contact domain registrar

### Development Resources
- **PHP Manual**: https://php.net/manual/
- **MySQL Docs**: https://dev.mysql.com/doc/
- **Stripe Docs**: https://stripe.com/docs/

## Regular Maintenance Tasks

### Weekly
- [ ] Check error logs
- [ ] Monitor disk space
- [ ] Review security logs
- [ ] Backup database

### Monthly
- [ ] Update software/packages
- [ ] Review user accounts
- [ ] Check email deliverability
- [ ] Monitor site performance

### Quarterly
- [ ] Rotate API keys
- [ ] Review and update passwords
- [ ] Audit user permissions
- [ ] Update documentation

## Performance Monitoring

### Key Metrics to Track
- **Page Load Time**: < 3 seconds
- **Database Query Time**: < 100ms
- **Error Rate**: < 1%
- **Uptime**: > 99.9%
- **Email Delivery Rate**: > 95%

### Tools
- **Google Analytics**: User behavior
- **Google Search Console**: Search performance
- **GTmetrix**: Page speed
- **Uptime Robot**: Site monitoring
- **Stripe Dashboard**: Payment analytics

## Security Best Practices

### Password Requirements
- Minimum 12 characters
- Include uppercase, lowercase, numbers, symbols
- No dictionary words
- Unique for each service

### Regular Security Tasks
- [ ] Change passwords quarterly
- [ ] Monitor failed login attempts
- [ ] Keep software updated
- [ ] Review access logs
- [ ] Backup verification

---

**Remember**: Keep this guide updated as you make changes to your system. Always test changes in a development environment first!