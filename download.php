<?php
require_once 'config.php';
requireUserLogin();

$user = getCurrentUser();

if (!isset($_GET['id'])) {
    header('Location: downloads.php');
    exit();
}

$product_id = (int)$_GET['id'];

// Verify user has access to this download
$stmt = $pdo->prepare("SELECT p.*, ud.download_count 
                      FROM products p 
                      JOIN orders o ON p.id = o.product_id 
                      JOIN user_downloads ud ON p.id = ud.product_id 
                      WHERE p.id = ? AND o.user_id = ? AND o.status = 'completed' AND ud.user_id = ?");
$stmt->execute([$product_id, $user['id'], $user['id']]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = 'You do not have access to this download.';
    header('Location: downloads.php');
    exit();
}

// Check download limit (10 downloads per file)
if ($product['download_count'] >= 10) {
    $_SESSION['error'] = 'You have reached the maximum download limit for this file.';
    header('Location: downloads.php');
    exit();
}

// Update download count and last download time
$stmt = $pdo->prepare("UPDATE user_downloads SET download_count = download_count + 1, last_download = NOW() WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user['id'], $product_id]);

// In a real implementation, you would serve the actual file
// For demo purposes, we'll create a simple PDF file
$filename = preg_replace('/[^A-Za-z0-9-]+/', '-', $product['name']) . '.pdf';
$content = generate_demo_pdf_content($product);

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output PDF content
echo $content;
exit();

function generate_demo_pdf_content($product) {
    // Create a simple PDF content for demo purposes
    $content = "%PDF-1.4\n";
    $content .= "1 0 obj\n";
    $content .= "<<\n";
    $content .= "/Type /Catalog\n";
    $content .= "/Pages 2 0 R\n";
    $content .= ">>\n";
    $content .= "endobj\n";
    $content .= "2 0 obj\n";
    $content .= "<<\n";
    $content .= "/Type /Pages\n";
    $content .= "/Kids [3 0 R]\n";
    $content .= "/Count 1\n";
    $content .= ">>\n";
    $content .= "endobj\n";
    $content .= "3 0 obj\n";
    $content .= "<<\n";
    $content .= "/Type /Page\n";
    $content .= "/Parent 2 0 R\n";
    $content .= "/MediaBox [0 0 612 792]\n";
    $content .= "/Contents 4 0 R\n";
    $content .= "/Resources <<>>\n";
    $content .= ">>\n";
    $content .= "endobj\n";
    $content .= "4 0 obj\n";
    $content .= "<<\n";
    $content .= "/Length 44\n";
    $content .= ">>\n";
    $content .= "stream\n";
    $content .= "BT\n";
    $content .= "/F1 24 Tf\n";
    $content .= "100 700 Td\n";
    $content .= "(" . addslashes($product['name']) . ") Tj\n";
    $content .= "ET\n";
    $content .= "endstream\n";
    $content .= "endobj\n";
    $content .= "xref\n";
    $content .= "0 5\n";
    $content .= "0000000000 65535 f\n";
    $content .= "0000000010 00000 n\n";
    $content .= "0000000053 00000 n\n";
    $content .= "0000000125 00000 n\n";
    $content .= "0000000185 00000 n\n";
    $content .= "trailer\n";
    $content .= "<<\n";
    $content .= "/Size 5\n";
    $content .= "/Root 1 0 R\n";
    $content .= ">>\n";
    $content .= "startxref\n";
    $content .= "279\n";
    $content .= "%%EOF\n";
    
    return $content;
}
?>