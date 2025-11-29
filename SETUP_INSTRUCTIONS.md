# Mental Edge Trading Admin Panel Setup Instructions

## Overview
This document provides comprehensive instructions for setting up the Mental Edge Trading admin panel with demo/live toggle functionality.

## Folder Structure
```
/mnt/okcomputer/output/
├── admin_dashboard.php          # Main admin dashboard with demo/live toggle
├── admin_login.html             # Updated login page (changed from "Admin Login" to "LOGIN")
├── about.html                   # About page with centered Sarah Banwart text
├── config.php                   # Database configuration
├── admin_login.php              # PHP login handler
├── admin_logout.php             # Logout handler
├── database_setup.sql           # Database schema
├── SETUP_INSTRUCTIONS.md        # This file
└── (other existing files...)
```

## Local Development Setup

### 1. Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependencies)

### 2. Database Setup
```sql
-- Create database
CREATE DATABASE mentaledge_main;
USE mentaledge_main;

-- Run the database setup script
SOURCE database_setup.sql;

-- Insert admin user (password: admin123)
INSERT INTO admin_users (username, password_hash, email) VALUES 
('sarahadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@mentaledge.com');
```

### 3. Configuration
Update `config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mentaledge_main');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
```

### 4. File Permissions
```bash
# Set proper permissions
chmod 644 *.php
chmod 644 *.html
chmod 600 config.php  # Keep config secure
```

### 5. Start Local Server
```bash
# Using PHP built-in server
php -S localhost:8000

# Or using Apache/Nginx
# Point document root to /mnt/okcomputer/output
```

## cPanel Setup Instructions

### 1. Upload Files
1. Log into your cPanel account
2. Navigate to "File Manager"
3. Upload all files to your desired directory (usually `public_html/`)
4. Ensure the folder structure matches the layout above

### 2. Create Database
1. In cPanel, go to "MySQL Databases"
2. Create a new database (e.g., `username_mentaledge`)
3. Create a database user and assign to the database
4. Note down the database name, username, and password

### 3. Import Database
1. Go to "phpMyAdmin" in cPanel
2. Select your newly created database
3. Click "Import" tab
4. Upload and import `database_setup.sql`
5. Insert admin user:
```sql
INSERT INTO admin_users (username, password_hash, email) VALUES 
('sarahadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@yourdomain.com');
```

### 4. Update Configuration
1. Edit `config.php` in File Manager
2. Update database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_cpanel_username_mentaledge');
define('DB_USER', 'your_cpanel_username_dbuser');
define('DB_PASS', 'your_db_password');
```

### 5. Set Permissions
1. In File Manager, select all files
2. Click "Permissions" and set to 644
3. Set `config.php` to 600 for security

## Demo/Live Mode Functionality

### How It Works
- **Demo Mode**: Shows sample data, prevents destructive actions
- **Live Mode**: Shows real database data, allows full functionality
- Toggle switch at the top of admin dashboard switches between modes
- Mode state is stored in PHP session

### Features
1. **Visual Indicators**: 
   - Demo mode: Yellow warning banner
   - Live mode: Green success banner
   - Toggle switch shows current mode status

2. **Data Display**:
   - Demo: Static sample data
   - Live: Real database queries

3. **Action Restrictions**:
   - Demo: Shows alerts for destructive actions
   - Live: Executes actual database operations

## Accessing the Admin Panel

### Default Credentials
- **Username**: `sarahadmin`
- **Password**: `admin123`

### Login URL
- Local: `http://localhost:8000/admin_login.html`
- cPanel: `https://yourdomain.com/admin_login.html`

### First Login
1. Go to admin login page
2. Enter credentials
3. You'll be redirected to admin dashboard
4. Use the toggle switch to switch between demo and live modes

## Security Considerations

1. **Change Default Password**: Immediately change the admin password after first login
2. **Database Security**: Keep `config.php` permissions at 600
3. **HTTPS**: Use SSL certificate for production
4. **Regular Backups**: Backup database regularly
5. **Access Control**: Limit admin panel access by IP if possible

## Troubleshooting

### Common Issues
1. **Database Connection Error**:
   - Check credentials in `config.php`
   - Verify database exists and user has permissions

2. **Login Not Working**:
   - Ensure database has admin user record
   - Check PHP session configuration

3. **Toggle Not Working**:
   - Verify JavaScript is enabled
   - Check PHP session storage permissions

4. **Styles Not Loading**:
   - Verify CSS file paths
   - Check file permissions (should be 644)

### Debug Mode
Enable PHP error reporting for troubleshooting:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Support
For technical support or questions about the setup, refer to:
- Original documentation files
- PHP/MySQL documentation
- cPanel documentation

## Next Steps
After setup is complete:
1. Test demo mode functionality
2. Switch to live mode and verify real data display
3. Create your first blog post
4. Set up email notifications
5. Configure analytics integration

---
*Last updated: November 2024*