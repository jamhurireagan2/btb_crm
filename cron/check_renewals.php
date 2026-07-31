<?php
// This file should be run daily via cron job
require_once '../config/database.php';
require_once '../includes/send_email.php';

// Log file
$log_file = '../logs/email_log.txt';
$log_dir = dirname($log_file);

// Create logs directory if it doesn't exist
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Check and send renewal emails
$result = checkAndSendRenewals();

$log_message = date('Y-m-d H:i:s') . " - Sent: {$result['sent']}, Failed: {$result['failed']}\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

echo "Renewal reminders sent: {$result['sent']}, Failed: {$result['failed']}\n";
?>