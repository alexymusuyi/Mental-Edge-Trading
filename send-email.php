<?php
// SECURITY CHECK
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.html");
    exit();
}

// SANITIZE INPUT
$name    = htmlspecialchars(trim($_POST["name"]));
$email   = htmlspecialchars(trim($_POST["email"]));
$reason  = htmlspecialchars(trim($_POST["reason"]));
$message = htmlspecialchars(trim($_POST["message"]));

// VALIDATE
if (empty($name) || empty($email) || empty($message)) {
    header("Location: contact.html?error=1");
    exit();
}

$to = "alexymusuyi@gmail.com";  // <--- your email
$subject = "New Contact Form Message from Mental Edge Trading";

$body = "
A new contact form submission:

Name: $name
Email: $email
Reason: $reason

Message:
$message

------------------------
Sent from MentalEdgeTrading.com
";

$headers  = "From: noreply@mentaledgetrading.com\r\n";
$headers .= "Reply-To: $email\r\n";

if (mail($to, $subject, $body, $headers)) {
    header("Location: contact.html?success=1");
    exit();
} else {
    header("Location: contact.html?error=1");
    exit();
}
?>
