<?php
require_once 'config.php';

// Test page to verify demo/live toggle functionality

// Check current mode
$is_demo_mode = isset($_SESSION['demo_mode']) && $_SESSION['demo_mode'] === true;

// Handle mode toggle
if (isset($_GET['toggle'])) {
    $_SESSION['demo_mode'] = !$is_demo_mode;
    header('Location: test_demo_toggle.php');
    exit();
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Demo/Live Toggle Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .status { padding: 20px; margin: 20px 0; border-radius: 8px; }
        .demo { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .live { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        button { padding: 10px 20px; font-size: 16px; cursor: pointer; }
        .toggle-btn { background: #666A10; color: white; border: none; border-radius: 5px; }
        .toggle-btn:hover { background: #555915; }
    </style>
</head>
<body>
    <h1>Demo/Live Toggle Test</h1>
    
    <div class='status " . ($is_demo_mode ? 'demo' : 'live') . "'>
        <h2>Current Mode: " . ($is_demo_mode ? 'DEMO' : 'LIVE') . "</h2>
        <p>" . ($is_demo_mode ? 'You are viewing demo data' : 'You are viewing live data') . "</p>
    </div>
    
    <form method='get'>
        <button type='submit' name='toggle' value='1' class='toggle-btn'>
            Switch to " . ($is_demo_mode ? 'LIVE' : 'DEMO') . " Mode
        </button>
    </form>
    
    <br><br>
    <a href='admin_dashboard.php'>Go to Admin Dashboard</a>
</body>
</html>";
?>