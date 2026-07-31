<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../includes/send_email.php';

echo "Starting cron job...\n";

// Get clients expiring within 30 days with emails
$sql = "SELECT * FROM clients 
        WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
        AND expiry_date >= CURDATE() 
        AND email IS NOT NULL 
        AND email != ''";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$clients = $stmt->fetchAll();

echo "Found " . count($clients) . " clients with expiring policies.\n";

$sent = 0;
$failed = 0;

foreach ($clients as $client) {
    echo "Processing: " . $client['full_name'] . " (" . $client['email'] . ")\n";
    
    $result = sendRenewalReminder($client);
    
    if ($result) {
        $sent++;
        echo "  ✅ Email sent\n";
    } else {
        $failed++;
        echo "  ❌ Failed to send\n";
    }
}

echo "Renewal reminders sent: $sent, Failed: $failed\n";
?>