<?php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com'); // or your SMTP host
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com'); // Your email
define('SMTP_PASSWORD', 'your_app_password'); // App password
define('SMTP_FROM_EMAIL', 'your_email@gmail.com');
define('SMTP_FROM_NAME', 'Client Management System');

// Email templates
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
            .status-urgent { color: #dc2626; font-weight: bold; }
            .status-warning { color: #f59e0b; font-weight: bold; }
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
                    <p><strong>Days Remaining:</strong> <span class='" . ($days_remaining <= 7 ? 'status-urgent' : 'status-warning') . "'>$days_remaining days</span></p>
                    <p><strong>Status:</strong> $status</p>
                </div>
                
                <p>$message</p>
                
                <p style='margin-top: 20px;'>
                    <a href='https://client-management-system.page.gd/user/' class='btn'>View My Policy</a>
                </p>
                
                <p style='margin-top: 20px; font-size: 14px; color: #666;'>
                    <strong>📞 Contact Us:</strong> 0712345678<br>
                    <strong>📧 Email:</strong> info@btbinsurance.com
                </p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Client Management System. All rights reserved.</p>
                <p>This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>