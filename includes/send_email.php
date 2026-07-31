<?php
// Email Configuration - Using SendGrid

define('SENDGRID_API_KEY', 'YOUR_SENDGRID_API_KEY'); // Replace with your API key
define('SENDGRID_FROM_EMAIL', 'your_verified_email@gmail.com');
define('SENDGRID_FROM_NAME', 'Client Management System');

function sendEmail($to_email, $to_name, $subject, $html_content) {
    $api_key = SENDGRID_API_KEY;
    
    $data = [
        'personalizations' => [
            [
                'to' => [
                    ['email' => $to_email, 'name' => $to_name]
                ],
                'subject' => $subject
            ]
        ],
        'from' => [
            'email' => SENDGRID_FROM_EMAIL,
            'name' => SENDGRID_FROM_NAME
        ],
        'content' => [
            [
                'type' => 'text/html',
                'value' => $html_content
            ]
        ]
    ];
    
    $json = json_encode($data);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json)
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code >= 200 && $http_code < 300;
}

function getRenewalEmailTemplate($client, $days_remaining) {
    $status = $days_remaining <= 7 ? '⚠️ URGENT' : '📋 Reminder';
    $message = $days_remaining <= 7 
        ? 'Your policy expires in less than a week! Please contact us immediately to renew.'
        : 'Please contact us to discuss your renewal options.';

    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc2626; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8fafc; padding: 30px; border-radius: 0 0 10px 10px; }
            .policy-details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #dc2626; }
            .btn { background: #dc2626; color: white; padding: 10px 25px; text-decoration: none; border-radius: 5px; display: inline-block; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>📋 Policy Renewal Reminder</h2>
            </div>
            <div class='content'>
                <h3>Hello {$client['full_name']},</h3>
                <p>This is a reminder that your insurance policy is expiring soon.</p>
                
                <div class='policy-details'>
                    <p><strong>Policy Number:</strong> {$client['policy_number']}</p>
                    <p><strong>Policy Type:</strong> {$client['policy_type']}</p>
                    <p><strong>Expiry Date:</strong> " . date('d M Y', strtotime($client['expiry_date'])) . "</p>
                    <p><strong>Days Remaining:</strong> $days_remaining days</p>
                    <p><strong>Status:</strong> $status</p>
                </div>
                
                <p>$message</p>
                
                <p style='margin-top: 20px;'>
                    <a href='https://client-managent-ystem.page.gd/user/' class='btn'>View My Policy</a>
                </p>
                
                <p style='margin-top: 20px; font-size: 14px; color: #666;'>
                    <strong>📞 Contact Us:</strong> 0712345678<br>
                    <strong>📧 Email:</strong> info@btbinsurance.com
                </p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Client Management System. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

function sendRenewalReminder($client) {
    $today = new DateTime();
    $expiry = new DateTime($client['expiry_date']);
    $days_remaining = $today->diff($expiry)->days;
    
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
?>