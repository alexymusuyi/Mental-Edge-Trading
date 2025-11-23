# Mental Edge Trading - Admin Panel Setup

## Overview
This admin panel provides comprehensive blog management and analytics for the Mental Edge Trading website. Built with PHP and MySQL, it includes secure authentication, blog CRUD operations, and detailed analytics.

## Features

### 🔐 Security
- Secure PHP-based authentication system
- Password hashing with bcrypt
- Session management
- CSRF protection
- SQL injection prevention with PDO prepared statements

### 📝 Blog Management
- Create, edit, and delete blog posts
- Rich text content editor
- Category and tag management
- Draft and published status options
- SEO-friendly URL slugs

### 📊 Analytics Dashboard
- Real-time website statistics
- Page views and visitor tracking
- Popular blog posts analysis
- Interactive charts and graphs
- Performance metrics

### 🎨 Design
- Consistent design with the main website
- Responsive layout for all devices
- Clean, professional interface
- Intuitive navigation

## Installation Instructions

### 1. Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE mental_edge_trading;
   ```

2. **Run Setup Script**
   - Upload `setup_database.php` to your server
   - Access it via browser: `yourdomain.com/setup_database.php`
   - This will create all necessary tables and default admin user
   - **Important**: Delete `setup_database.php` after setup for security

### 2. Configuration

1. **Update Database Credentials**
   - Edit `config.php`
   - Update these constants with your database information:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'mental_edge_trading');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   ```

2. **Default Admin Login**
   - Username: `admin`
   - Password: `admin123`
   - **Change this immediately after first login**

### 3. File Structure
```
/mnt/okcomputer/output/
├── admin_login.php          # Admin login page
├── admin_dashboard.php      # Main dashboard
├── admin_blog.php          # Blog management
├── admin_edit_blog.php     # Edit blog posts
├── admin_analytics.php     # Analytics page
├── admin_settings.php      # Admin settings
├── admin_logout.php        # Logout script
├── config.php              # Database configuration
├── setup_database.php      # Database setup (delete after use)
├── index.html              # Main website (updated with login button)
├── blog.html               # Blog page (updated with login button)
├── book.html               # Book page (updated with login button)
├── contact.html            # Contact page (updated with login button)
├── podcast.html            # Podcast page (updated with login button)
├── about.html              # About page (created)
├── style.css               # Updated styles with login button
└── main.js                 # Main JavaScript
```

### 4. cPanel Integration

1. **Upload Files**
   - Upload all files to your cPanel file manager
   - Place them in the public_html directory

2. **Create MySQL Database**
   - Use cPanel MySQL Databases section
   - Create database and user
   - Assign user to database with all privileges

3. **Update Configuration**
   - Edit `config.php` with your cPanel database credentials

### 5. Security Considerations

- **Change Default Password**: Immediately change the admin password after first login
- **Delete Setup File**: Remove `setup_database.php` after installation
- **Regular Backups**: Backup your database regularly
- **Update Passwords**: Change database passwords periodically
- **File Permissions**: Set appropriate file permissions (644 for files, 755 for directories)

## Usage Guide

### Accessing Admin Panel
1. Navigate to `yourdomain.com/admin_login.php`
2. Login with your admin credentials
3. Use the dashboard to manage content and view analytics

### Managing Blog Posts
1. Go to "Blog Management" in the admin panel
2. Click "Create New Post" to add content
3. Use the editor to write your blog post
4. Set status to "Published" when ready
5. Edit existing posts by clicking the "Edit" button

### Viewing Analytics
- Dashboard shows real-time statistics
- Analytics page provides detailed charts
- Track page views, visitors, and popular content
- Monitor website performance over time

### Changing Admin Password
1. Go to "Settings" in the admin panel
2. Enter current password and new password
3. Click "Change Password" to update

## Technical Requirements

- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Server**: Apache or Nginx
- **Browser**: Modern browsers (Chrome, Firefox, Safari, Edge)

## Support

For technical support or questions about the admin panel:
1. Check the configuration in `config.php`
2. Ensure database credentials are correct
3. Verify file permissions are set correctly
4. Check PHP error logs for specific issues

## Security Features

- **Session Management**: Secure session handling with timeout
- **Password Hashing**: BCrypt encryption for passwords
- **Input Validation**: Server-side validation of all inputs
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Output encoding and sanitization
- **CSRF Protection**: Token-based form protection

## Future Enhancements

- Media upload for blog post images
- Advanced analytics with Google Analytics integration
- Multi-user admin system
- Email notifications for new comments
- SEO optimization tools
- Backup and restore functionality

---

**Note**: This admin panel is designed specifically for the Mental Edge Trading website and integrates seamlessly with the existing design and structure.