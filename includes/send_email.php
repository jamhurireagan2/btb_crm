<?php
require_once '../config/email.php';

function sendEmail($to_email, $to_name, $subject, $html_content) {
    // Using PHPMailer (you'll need to install it via composer)
    // For now, let's use a simple mail function
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    
    return mail($to_email, $subject, $html_content, $headers);
}

function sendRenewalReminder($client) {
    $today = new DateTime();
    $expiry = new DateTime($client['expiry_date']);
    $days_remaining = $today->diff($expiry)->days;
    
    // Only send if within 30 days and email exists
    if (empty($client['email']) || $days_remaining <= 0 || $days_remaining > 30) {
        return false;
    }
    
    $subject = "📋 Policy Renewal Reminder - " . date('d M Y', strtotime($client['expiry_date']));
    $html = getRenewalEmailTemplate($client, $days_remaining);
    
    return sendEmail(
        $client['email'],
        $client['full_name'],
        $subject,
        $html
    );
}

function getRenewalEmailTemplate($client, $days_remaining) {
    $status = $days_remaining <= 7 ? '⚠️ URGENT' : '📋 Reminder';
    $message = $days_remaining <= 7 
        ? 'Your policy expires in less than a week! Please contact us immediately to renew.'
        : 'Please contact us to discuss your renewal options.';

    return "
    <html>
    <body>
        <h2>Policy Renewal Reminder</h2>
        <p>Hello {$client['full_name']},</p>
        <p>Your policy is expiring soon.</p>
        <p><strong>Policy Number:</strong> {$client['policy_number']}</p>
        <p><strong>Expiry Date:</strong> " . date('d M Y', strtotime($client['expiry_date'])) . "</p>
        <p><strong>Days Remaining:</strong> $days_remaining</p>
        <p>$message</p>
        <p><a href='https://client-managent-ystem.page.gd/user/'>View My Policy</a></p>
    </body>
    </html>
    ";
}   