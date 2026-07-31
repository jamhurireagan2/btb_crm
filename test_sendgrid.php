<?php
require_once 'config/database.php';
require_once 'includes/send_email.php';

echo "<h1>📧 SendGrid Test</h1>";

// Get a client with email
$stmt = $pdo->prepare("SELECT * FROM clients WHERE email IS NOT NULL AND email != '' LIMIT 1");
$stmt->execute();
$client = $stmt->fetch();

if ($client) {
    echo "<p><strong>Client:</strong> {$client['full_name']}</p>";
    echo "<p><strong>Email:</strong> {$client['email']}</p>";
    
    $result = sendRenewalReminder($client);
    
    if ($result) {
        echo "<p style='color:green; font-weight:bold;'>✅ Email sent successfully to {$client['email']}!</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ Email failed to send.</p>";
        echo "<p>Check your SendGrid API key.</p>";
    }
} else {
    echo "<p style='color:red;'>❌ No clients with email addresses found.</p>";
}
?>