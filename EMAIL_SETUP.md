# Email Configuration Guide for Mental Edge Trading

## Overview
This guide will help you set up email functionality for your Mental Edge Trading website, enabling password resets, order confirmations, and other essential email notifications.

## Step 1: Choose Your Email Solution

### Option A: cPanel Email (Recommended for Beginners)
**Pros:**
- Free with most hosting plans
- Easy to set up
- Integrated with your hosting

**Cons:**
- Limited features
- May have sending limits
- Can affect deliverability

### Option B: External SMTP Service (Recommended for Professional Use)
**Popular Options:**
- **SendGrid** - 100 emails/day free, excellent deliverability
- **Mailgun** - 5,000 emails/month free for 3 months
- **Amazon SES** - Pay-as-you-go, very reliable
- **Gmail SMTP** - Free but limited to 500 emails/day

**Pros:**
- Better deliverability
- Advanced features
- Professional appearance
- Higher sending limits

**Cons:**
- May have costs
- Additional setup required

## Step 2: cPanel Email Setup

### 2.1 Create Email Account
1. Log into cPanel
2. Navigate to "Email Accounts"
3. Click "Create" button
4. Set up your email:
   - Email: `noreply@yourdomain.com` or `support@yourdomain.com`
   - Set strong password
   - Set mailbox quota (1GB recommended)
5. Click "Create Account"

### 2.2 Get SMTP Settings
1. In "Email Accounts", find your new email
2. Click "Connect Devices"
3. Note down the SMTP settings:
   - SMTP Host: Usually `mail.yourdomain.com`
   - SMTP Port: 587 (TLS) or 465 (SSL)
   - Username: Your full email address
   - Password: The password you set

### 2.3 Update Configuration
Edit your `config.php`:

```php
// cPanel Email Configuration
define('SMTP_HOST', 'mail.yourdomain.com');
define('SMTP_USER', 'noreply@yourdomain.com');
define('SMTP_PASS', 'your-email-password');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

define('FROM_EMAIL', 'noreply@yourdomain.com');
define('FROM_NAME', 'Mental Edge Trading');
define('REPLY_TO_EMAIL', 'support@yourdomain.com');
```

## Step 3: SendGrid Setup (Recommended)

### 3.1 Create SendGrid Account
1. Go to [sendgrid.com](https://sendgrid.com)
2. Sign up for free account (100 emails/day)
3. Complete verification process
4. Navigate to Settings → API Keys

### 3.2 Create API Key
1. Click "Create API Key"
2. Name: "Mental Edge Trading Website"
3. Permissions: "Full Access"
4. Copy the generated API key (starts with `SG.`)

### 3.3 Update Configuration
```php
// SendGrid Configuration
define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_USER', 'apikey'); // Always 'apikey' for SendGrid
define('SMTP_PASS', 'SG.YOUR_SENDGRID_API_KEY');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

define('FROM_EMAIL', 'noreply@yourdomain.com');
define('FROM_NAME', 'Mental Edge Trading');
define('REPLY_TO_EMAIL', 'support@yourdomain.com');
```

### 3.4 Set Up Domain Authentication (Important!)
1. In SendGrid, go to Settings → Sender Authentication
2. Click "Authenticate Your Domain"
3. Follow the setup process:
   - Add DNS records to your domain
   - Wait for verification (can take up to 48 hours)

## Step 4: Mailgun Setup

### 4.1 Create Mailgun Account
1. Go to [mailgun.com](https://www.mailgun.com)
2. Sign up for free account
3. Add your domain for sending

### 4.2 Get SMTP Credentials
1. Navigate to Settings → SMTP
2. Note down:
   - SMTP Host: `smtp.mailgun.org`
   - Username: Your Mailgun username
   - Password: Your Mailgun password
   - Port: 587

### 4.3 Update Configuration
```php
// Mailgun Configuration
define('SMTP_HOST', 'smtp.mailgun.org');
define('SMTP_USER', 'your-mailgun-username');
define('SMTP_PASS', 'your-mailgun-password');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

## Step 5: Gmail SMTP Setup (Free Option)

### 5.1 Prepare Gmail Account
1. Use a dedicated Gmail account (not your personal one)
2. Enable 2-factor authentication
3. Generate an "App Password":
   - Go to Google Account → Security
   - Enable 2FA if not already
   - Go to "App passwords"
   - Generate password for "Mail"

### 5.2 Update Configuration
```php
// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-trading-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
```

**Note:** Gmail limits to 500 emails per day. For higher volume, use a professional service.

## Step 6: Update Email Functions

### 6.1 Install PHPMailer (If Not Already Included)
```bash
# Via Composer (if available)
composer require phpmailer/phpmailer

# Or download manually from https://github.com/PHPMailer/PHPMailer
```

### 6.2 Create Email Helper Functions
Create `email_functions.php`:

```php
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendEmail($to, $subject, $body, $isHTML = true) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(REPLY_TO_EMAIL, FROM_NAME);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        if ($isHTML) {
            $mail->AltBody = strip_tags($body);
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

function sendPasswordResetEmail($email, $resetToken) {
    $resetUrl = SITE_URL . '/reset_password.php?token=' . $resetToken;
    $subject = 'Password Reset - Mental Edge Trading';
    
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #2c3e50;'>Password Reset Request</h2>
            <p>Hello,</p>
            <p>We received a request to reset your password for your Mental Edge Trading account.</p>
            <p><strong>If you did not make this request, please ignore this email.</strong></p>
            <p>To reset your password, click the link below:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$resetUrl}' style='background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
            </p>
            <p>This link will expire in 1 hour for security reasons.</p>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
            <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
            <p style='font-size: 12px; color: #666;'>
                Best regards,<br>
                The Mental Edge Trading Team
            </p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

function sendOrderConfirmationEmail($email, $orderDetails) {
    $subject = 'Order Confirmation - Mental Edge Trading';
    
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #27ae60;'>Order Confirmed!</h2>
            <p>Thank you for your purchase!</p>
            <p><strong>Order Number:</strong> {$orderDetails['order_number']}</p>
            <p><strong>Product:</strong> {$orderDetails['product_name']}</p>
            <p><strong>Amount:</strong> \${$orderDetails['amount']}</p>
            
            <h3 style='color: #2c3e50;'>Access Your Purchase</h3>
            <p>You can access your purchased content by logging into your account and visiting the Downloads section.</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='" . SITE_URL . "/login.php' style='background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Login to Account</a>
            </p>
            
            <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
            <p style='font-size: 12px; color: #666;'>
                If you have any questions, please contact us at " . REPLY_TO_EMAIL . "<br>
                Best regards,<br>
                The Mental Edge Trading Team
            </p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

function sendWelcomeEmail($email, $username) {
    $subject = 'Welcome to Mental Edge Trading!';
    
    $body = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #27ae60;'>Welcome to Mental Edge Trading!</h2>
            <p>Hello {$username},</p>
            <p>Thank you for joining Mental Edge Trading! Your account has been successfully created.</p>
            
            <h3 style='color: #2c3e50;'>What's Next?</h3>
            <ul>
                <li>Explore our educational blog posts</li>
                <li>Browse our premium trading guides</li>
                <li>Access your account dashboard</li>
            </ul>
            
            <p style='text-align: center; margin: 30px 0;'>
                <a href='" . SITE_URL . "/dashboard.php' style='background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Visit Dashboard</a>
            </p>
            
            <hr style='margin: 30px 0; border: none; border-top: 1px solid #eee;'>
            <p style='font-size: 12px; color: #666;'>
                If you have any questions, please contact us at " . REPLY_TO_EMAIL . "<br>
                Best regards,<br>
                The Mental Edge Trading Team
            </p>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $body);
}
?>
```

## Step 7: Update Your PHP Files

### 7.1 Update forgot_password.php
```php
<?php
require_once 'config.php';
require_once 'email_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update user with reset token
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);
    
    if ($stmt->rowCount() > 0) {
        // Send email
        if (sendPasswordResetEmail($email, $token)) {
            $_SESSION['success'] = 'Password reset instructions have been sent to your email.';
        } else {
            $_SESSION['error'] = 'Failed to send email. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Email address not found.';
    }
    
    header('Location: forgot_password.php');
    exit();
}
?>
```

### 7.2 Update register.php
```php
<?php
require_once 'config.php';
require_once 'email_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... validation code ...
    
    // Create user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
    
    if ($stmt->rowCount() > 0) {
        // Send welcome email
        sendWelcomeEmail($email, $username);
        
        $_SESSION['success'] = 'Registration successful! Please login.';
        header('Location: login.php');
        exit();
    }
}
?>
```

### 7.3 Update payment.php
```php
<?php
require_once 'config.php';
require_once 'email_functions.php';

// After successful payment
if ($paymentIntent->status === 'succeeded') {
    // ... order processing code ...
    
    // Send confirmation email
    $orderDetails = [
        'order_number' => $orderNumber,
        'product_name' => $product['name'],
        'amount' => number_format($amount / 100, 2)
    ];
    
    sendOrderConfirmationEmail($userEmail, $orderDetails);
}
?>
```

## Step 8: Testing Email Functionality

### 8.1 Create Test Script
Create `test_email.php`:

```php
<?php
require_once 'config.php';
require_once 'email_functions.php';

// Test password reset
$testToken = bin2hex(random_bytes(32));
$result = sendPasswordResetEmail('your-test-email@example.com', $testToken);

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check error logs.";
}
?>
```

### 8.2 Common Email Testing Tools
- **Mail Tester**: https://www.mail-tester.com
- **Mailtrap**: Free email testing service
- **Google Postmaster Tools**: Monitor deliverability

## Step 9: Email Best Practices

### 9.1 Improve Deliverability
1. **Set up SPF Record** (in DNS):
   ```
   v=spf1 include:_spf.google.com include:mailgun.org ~all
   ```

2. **Set up DKIM** (provided by your email service)

3. **Set up DMARC**:
   ```
   v=DMARC1; p=quarantine; rua=mailto:dmarc@yourdomain.com
   ```

### 9.2 Email Content Best Practices
- Keep subject lines clear and relevant
- Use a professional email template
- Include unsubscribe links for marketing emails
- Don't use spam trigger words
- Keep image-to-text ratio balanced

### 9.3 Monitor and Maintain
- Check spam folder rates
- Monitor bounce rates
- Remove invalid email addresses
- Regularly test email functionality

## Step 10: Troubleshooting

### Common Issues

**"Could not connect to SMTP host"**
- Check SMTP credentials
- Verify port is correct (587 for TLS, 465 for SSL)
- Check firewall settings
- Try different SMTP host

**"Authentication failed"**
- Verify username and password
- Check if 2FA is required
- For Gmail, use App Password
- Check account status

**"Connection timed out"**
- Check server firewall
- Verify outbound connections allowed
- Try different port
- Contact hosting provider

**Emails going to spam**
- Set up SPF, DKIM, DMARC
- Use professional email service
- Avoid spam trigger words
- Send from verified domain

### Debug Mode
Enable PHPMailer debug mode:

```php
$mail->SMTPDebug = 2; // 0 = off, 1 = client, 2 = client and server
$mail->Debugoutput = 'html';
```

## Support Resources

### Email Service Support
- **SendGrid**: https://sendgrid.com/docs/
- **Mailgun**: https://documentation.mailgun.com/
- **Amazon SES**: https://docs.aws.amazon.com/ses/
- **PHPMailer**: https://github.com/PHPMailer/PHPMailer

### Testing Tools
- **Mail Tester**: https://www.mail-tester.com/
- **MX Toolbox**: https://mxtoolbox.com/
- **Google Postmaster**: https://postmaster.google.com/

---

**Remember:** Email deliverability is crucial for user experience. Always test thoroughly and monitor your email performance regularly. Consider using a professional email service for better deliverability and features.