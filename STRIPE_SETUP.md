# Stripe Payment Integration Setup Guide

## Overview
This guide will help you set up Stripe payment processing for your Mental Edge Trading website, enabling you to sell digital products and accept payments securely.

## Step 1: Stripe Account Setup

### 1.1 Create Your Stripe Account
1. Go to [stripe.com](https://stripe.com)
2. Click "Start now" or "Sign in"
3. Complete the registration process
4. Verify your email address

### 1.2 Complete Business Verification
1. Navigate to Settings → Account details
2. Complete all required business information:
   - Business type (Individual/Sole proprietorship recommended for digital products)
   - Business website (your domain)
   - Business description ("Digital trading education products")
   - Customer support phone number
   - Business address

### 1.3 Set Up Two-Factor Authentication
1. Go to Settings → Security
2. Enable two-factor authentication
3. This is required for accessing live API keys

## Step 2: API Keys Configuration

### 2.1 Get Your API Keys
1. Go to Developers → API keys
2. You'll see two sets of keys:
   - **Test mode** (for development)
   - **Live mode** (for production)

### 2.2 Test Mode Setup (Recommended for Initial Testing)
1. Ensure you're in "Test mode" (toggle in top-right)
2. Copy the following test keys:
   - Publishable key: `pk_test_...`
   - Secret key: `sk_test_...`

### 2.3 Update Your Configuration
Edit your `config.php` file:

```php
// For testing
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_TEST_PUBLISHABLE_KEY');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_TEST_SECRET_KEY');

define('STRIPE_MODE', 'test'); // Change to 'live' for production
```

## Step 3: Webhook Configuration

### 3.1 Create Webhook Endpoint
1. Go to Developers → Webhooks
2. Click "Add endpoint"
3. Set endpoint URL: `https://yourdomain.com/payment.php`
4. Select events to listen for:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`

### 3.2 Get Webhook Secret
After creating the endpoint, copy the "Signing secret" (starts with `whsec_`)

### 3.3 Update Configuration
Add to your `config.php`:

```php
define('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET');
```

## Step 4: Product Configuration

### 4.1 Define Your Products
Update the products array in `payment.php`:

```php
$products = [
    'trading-psychology-guide' => [
        'name' => 'Trading Psychology Mastery Guide',
        'price' => 9900, // Amount in cents ($99.00)
        'description' => 'Complete comprehensive guide to mastering trading psychology',
        'image' => 'https://yourdomain.com/images/products/trading-psychology-guide.jpg',
        'file_path' => 'downloads/trading-psychology-mastery.pdf'
    ],
    'risk-management-strategies' => [
        'name' => 'Advanced Risk Management Strategies',
        'price' => 14900, // $149.00
        'description' => 'Professional-level risk management course',
        'image' => 'https://yourdomain.com/images/products/risk-management-strategies.jpg',
        'file_path' => 'downloads/risk-management-strategies.pdf'
    ]
];
```

### 4.2 Upload Product Files
1. Create a `downloads` directory in your web root
2. Upload your PDF files to this directory
3. Set proper file permissions (644)
4. Protect the directory with `.htaccess`:

```apache
<FilesMatch "\.(pdf|zip)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>
```

## Step 5: Testing Payment Flow

### 5.1 Test Card Numbers
Use these test card numbers (Stripe provides):

**Successful Payments:**
- `4242 4242 4242 4242` (Visa)
- `4000 0566 5566 5556` (Visa debit)
- `5555 5555 5555 4444` (Mastercard)

**Declined Payments:**
- `4000 0000 0000 0002` (Generic decline)
- `4000 0000 0000 9995` (Insufficient funds)

**3D Secure:**
- `4000 0000 0000 3220` (Requires authentication)

For all test cards:
- Use any future expiry date
- Use any 3-digit CVC (4 digits for Amex)
- Use any ZIP code

### 5.2 Test the Complete Flow
1. Create a test user account on your site
2. Add a product to cart
3. Use test card `4242 4242 4242 4242`
4. Complete the purchase
5. Verify:
   - Payment appears in Stripe Dashboard
   - Order is recorded in your database
   - User can access downloads
   - Email notifications are sent

## Step 6: Going Live

### 6.1 Complete Business Verification
Before going live, ensure:
- All business information is complete
- Bank account is connected
- Identity verification is complete
- Website is fully functional

### 6.2 Switch to Live Mode
1. In Stripe Dashboard, toggle to "Live mode"
2. Get your live API keys:
   - Publishable key: `pk_live_...`
   - Secret key: `sk_live_...`

3. Update `config.php`:
```php
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_YOUR_LIVE_PUBLISHABLE_KEY');
define('STRIPE_SECRET_KEY', 'sk_live_YOUR_LIVE_SECRET_KEY');
define('STRIPE_MODE', 'live');
```

4. Create live webhook endpoint (same process as test)

### 6.3 Test Live Transaction
1. Make a small real purchase ($1-2)
2. Use a real credit card
3. Verify the complete flow works
4. Refund the test transaction

## Step 7: Security Best Practices

### 7.1 Secure Your Keys
- Never expose secret keys in client-side code
- Use environment variables for sensitive data
- Regularly rotate API keys
- Monitor API key usage in Stripe Dashboard

### 7.2 Implement Rate Limiting
Add rate limiting to prevent abuse:

```php
// In payment.php
session_start();
if (!isset($_SESSION['payment_attempts'])) {
    $_SESSION['payment_attempts'] = 0;
}

if ($_SESSION['payment_attempts'] > 5) {
    die('Too many payment attempts. Please try again later.');
}
```

### 7.3 Validate Webhooks
Always verify webhook signatures:

```php
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, STRIPE_WEBHOOK_SECRET
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}
```

## Step 8: Monitoring and Maintenance

### 8.1 Set Up Monitoring
1. Enable email notifications in Stripe
2. Set up failed payment alerts
3. Monitor dispute rates
4. Track conversion rates

### 8.2 Regular Maintenance
- Review failed payments weekly
- Update webhook endpoints if URLs change
- Monitor for suspicious activity
- Keep PHP Stripe library updated

### 8.3 Common Issues and Solutions

**"No such customer" Error**
- Ensure customer exists before charging
- Handle customer creation properly

**Webhook Not Receiving**
- Check endpoint URL is correct
- Verify webhook secret is correct
- Check server error logs

**Payment Intent Failures**
- Validate payment method before confirming
- Handle authentication requirements
- Implement proper error messages

## Step 9: Advanced Features

### 9.1 Subscription Products
For recurring payments:

```php
$subscription = \Stripe\Subscription::create([
    'customer' => $customer->id,
    'items' => [['price' => 'price_123']],
    'payment_behavior' => 'default_incomplete',
    'expand' => ['latest_invoice.payment_intent'],
]);
```

### 9.2 Customer Portal
Allow customers to manage payments:

```php
$session = \Stripe\BillingPortal\Session::create([
    'customer' => $customer->id,
    'return_url' => 'https://yourdomain.com/dashboard.php',
]);
```

### 9.3 Automatic Tax Calculation
Enable automatic tax:

```php
$payment_intent = \Stripe\PaymentIntent::create([
    'amount' => 9900,
    'currency' => 'usd',
    'automatic_payment_methods' => ['enabled' => true],
    'customer' => $customer->id,
]);
```

## Support and Resources

### Stripe Resources
- [Stripe Documentation](https://stripe.com/docs)
- [API Reference](https://stripe.com/docs/api)
- [Testing Guide](https://stripe.com/docs/testing)
- [Webhook Best Practices](https://stripe.com/docs/webhooks/best-practices)

### Getting Help
- Stripe Support: support@stripe.com
- Technical Issues: Check Stripe status page
- Integration Questions: Stripe Discord community

---

**Remember:** Always test thoroughly in test mode before going live. Keep your API keys secure and monitor your Stripe Dashboard regularly for any issues or unusual activity.