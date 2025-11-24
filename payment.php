<?php
require_once 'config.php';
requireUserLogin();

// This is a demo payment page - in production, you would integrate with Stripe
$user = getCurrentUser();

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Get product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = true");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: book.html');
    exit();
}

// Generate order ID for demo
$order_id = 'ORDER_' . time() . '_' . $user['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mental Edge Trading</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        :root {
            --bg: #f3f0ea;
            --bg-soft: #f7f4ee;
            --bg-card: #ffffff;
            --text-main: #1c1c1f;
            --text-muted: #6c6a72;
            --accent: #666A10;
            --accent-soft: #f8e0ea;
            --border-subtle: #e0ddd5;
            --shadow-soft: 0 18px 40px rgba(12, 8, 4, 0.1);
            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-pill: 999px;
            --transition-fast: 0.2s ease-out;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
        }

        /* Header */
        .site-header {
            background: var(--bg-card);
            box-shadow: var(--shadow-soft);
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .username {
            font-weight: 500;
        }

        .logout-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-fast);
        }

        .logout-btn:hover {
            background: #555915;
        }

        /* Main Layout */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
        }

        /* Order Summary */
        .order-summary {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .summary-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .summary-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .product-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            background: var(--bg-soft);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 1.5rem;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .product-description {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent);
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Checkout Form */
        .checkout-form {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 24px;
        }

        .form-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .form-section {
            margin-bottom: 24px;
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--accent);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            background: var(--bg-soft);
            transition: border-color var(--transition-fast);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
        }

        /* Payment Section */
        .payment-section {
            background: var(--bg-soft);
            border-radius: var(--radius-md);
            padding: 20px;
            margin-bottom: 24px;
        }

        .payment-methods {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .payment-method {
            flex: 1;
            padding: 12px;
            border: 2px solid var(--border-subtle);
            border-radius: var(--radius-md);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .payment-method.selected {
            border-color: var(--accent);
            background: var(--accent-soft);
        }

        .payment-method i {
            font-size: 1.5rem;
            color: var(--accent);
            margin-bottom: 8px;
        }

        /* Stripe Elements */
        .stripe-card {
            padding: 12px 16px;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            background: white;
        }

        /* Demo Notice */
        .demo-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #856404;
        }

        .demo-notice i {
            margin-right: 8px;
        }

        /* Button */
        .btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: var(--radius-pill);
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(102, 106, 16, 0.3);
        }

        .btn:disabled {
            background: var(--text-muted);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Security Badges */
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-subtle);
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .order-summary {
                position: static;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="index.html" class="logo">
                <i class="fas fa-brain"></i> Mental Edge Trading
            </a>
            <div class="user-info">
                <span class="username">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="checkout-container">
        <!-- Checkout Form -->
        <div class="checkout-form">
            <div class="form-header">
                <h2 class="form-title">Checkout</h2>
                <p>Complete your purchase</p>
            </div>

            <!-- Demo Notice -->
            <div class="demo-notice">
                <i class="fas fa-info-circle"></i>
                <strong>Demo Mode:</strong> This is a demonstration of the payment system. In production, this would integrate with Stripe for real payments.
            </div>

            <!-- Billing Information -->
            <div class="form-section">
                <h3 class="form-section-title">Billing Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="form-section">
                <h3 class="form-section-title">Payment Method</h3>
                <div class="payment-section">
                    <div class="payment-methods">
                        <div class="payment-method selected" data-method="card">
                            <i class="fas fa-credit-card"></i>
                            <div>Credit Card</div>
                        </div>
                        <div class="payment-method" data-method="paypal">
                            <i class="fab fa-paypal"></i>
                            <div>PayPal</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="card-element">Credit or debit card</label>
                        <div class="stripe-card" id="card-element">
                            <!-- Stripe Elements will create form elements here -->
                            <div style="padding: 12px; color: var(--text-muted);">
                                <i class="fas fa-credit-card"></i> Demo: Card details would be entered here
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complete Order -->
            <button type="button" class="btn" onclick="processPayment()">
                <i class="fas fa-lock"></i>
                Complete Order - $<?php echo number_format($product['price'], 2); ?>
            </button>

            <!-- Security Badges -->
            <div class="security-badges">
                <div class="security-badge">
                    <i class="fas fa-shield-alt"></i>
                    SSL Encrypted
                </div>
                <div class="security-badge">
                    <i class="fas fa-lock"></i>
                    Secure Payment
                </div>
                <div class="security-badge">
                    <i class="fas fa-check-circle"></i>
                    Money Back Guarantee
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <div class="summary-header">
                <h3 class="summary-title">Order Summary</h3>
            </div>

            <div class="product-item">
                <div class="product-image">
                    <i class="fas fa-book"></i>
                </div>
                <div class="product-info">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-description"><?php echo htmlspecialchars($product['description']); ?></div>
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                </div>
            </div>

            <div class="summary-total">
                <span>Total</span>
                <span>$<?php echo number_format($product['price'], 2); ?></span>
            </div>

            <!-- Product Features -->
            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                <h4 style="margin-bottom: 12px; font-size: 0.9rem; font-weight: 600;">What's Included:</h4>
                <ul style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    <li>Instant digital download</li>
                    <li>Lifetime access to updates</li>
                    <li>PDF format (works on all devices)</li>
                    <li>30-day money-back guarantee</li>
                    <li>Priority customer support</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Initialize Stripe (demo)
        const stripe = Stripe('pk_test_demo_key'); // Replace with your actual Stripe publishable key
        
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        // Process payment
        function processPayment() {
            // Show loading state
            const button = document.querySelector('.btn');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            button.disabled = true;

            // Simulate payment processing
            setTimeout(() => {
                // In a real implementation, you would:
                // 1. Create a Stripe payment intent
                // 2. Confirm the payment with Stripe
                // 3. Handle the payment result
                
                // For demo, just show success
                alert('Payment processed successfully! In a real implementation, you would be redirected to the download page.');
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
                
                // Redirect to downloads page
                window.location.href = 'downloads.php';
            }, 2000);
        }

        // Form validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = 'var(--border-subtle)';
                }
            });
        });
    </script>
</body>
</html>