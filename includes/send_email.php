<?php
require_once '../config/email.php';

function sendEmail($to_email, $to_name, $subject, $html_content) {
    // Using PHPMailer for reliable email sending
    require_once '../vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_content;
        $mail->AltBody = strip_tags($html_content);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendRenewalReminder($client) {
    $today = new DateTime();
    $expiry = new DateTime($client['expiry_date']);
    $days_remaining = $today->diff($expiry)->days;
    
    if ($days_remaining <= 0) return false;
    if ($days_remaining > 30) return false;
    
    $subject = "📋 Policy Renewal Reminder - " . date('d M Y', strtotime($client['expiry_date']));
    $html = getRenewalEmailTemplate($client, $days_remaining);
    
    return sendEmail(
        $client['email'],
        $client['full_name'],
        $subject,
        $html
    );
}

function checkAndSendRenewals() {
    global $pdo;
    
    // Get clients expiring in 30, 14, and 7 days
    $sql = "SELECT * FROM clients 
            WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
            AND expiry_date >= CURDATE() 
            AND email IS NOT NULL 
            AND email != ''";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $clients = $stmt->fetchAll();
    
    $sent = 0;
    $failed = 0;
    
    foreach ($clients as $client) {
        if (sendRenewalReminder($client)) {
            $sent++;
        } else {
            $failed++;
        }
    }
    
    return ['sent' => $sent, 'failed' => $failed];
}
?>