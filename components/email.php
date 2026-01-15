<?php
/**
 * Email Configuration for Seraph
 * PHPMailer configuration for sending emails via Hostinger
 */

/**
 * Send email using PHPMailer
 *
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $altBody Plain text alternative body
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $altBody = '') {
    // Check if PHPMailer is available
    $vendorAutoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($vendorAutoload)) {
        return [
            'success' => false,
            'message' => 'PHPMailer not installed. Run: composer install'
        ];
    }

    require_once $vendorAutoload;

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        $mail->Timeout    = 30; // 30 second timeout
        $mail->SMTPDebug  = 0;  // Disable debug output

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"
        ];
    }
}

/**
 * Send welcome email to new subscriber
 */
function sendWelcomeEmail($name, $email) {
    $subject = 'Welcome to the Seraph Waitlist!';

    $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #484848; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #FEF3E2 0%, #FFFBF5 100%); padding: 30px; text-align: center; }
        .logo { font-size: 32px; font-weight: bold; color: #D97706; margin-bottom: 10px; }
        .content { background: white; padding: 30px; border-left: 4px solid #D97706; }
        .benefits { background: #FFFBF5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .benefit-item { margin: 10px 0; padding-left: 25px; position: relative; }
        .benefit-item:before { content: "✓"; position: absolute; left: 0; color: #D97706; font-weight: bold; }
        .footer { text-align: center; padding: 20px; color: #8A8A8A; font-size: 12px; }
        .button { display: inline-block; background: #D97706; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SERAPH</div>
            <p style="color: #92400E; margin: 0;">Non-Fluoride Toothpaste</p>
        </div>

        <div class="content">
            <h2 style="color: #D97706;">Welcome, {$name}!</h2>

            <p>Thank you for joining the Seraph waitlist!</p>

            <p>You're now on the list to be among the first to experience our non-fluoride toothpaste.</p>

            <div class="benefits">
                <h3 style="color: #92400E; margin-top: 0;">As a waitlist member, you'll receive:</h3>
                <div class="benefit-item">Exclusive launch pricing for early waitlist members</div>
                <div class="benefit-item">First access when we launch</div>
                <div class="benefit-item">Updates on our natural oral care journey</div>
                <div class="benefit-item">Tips for maintaining a healthier smile</div>
            </div>

            <p style="color: #484848; font-style: italic; border-left: 3px solid #F59E0B; padding-left: 15px; margin: 20px 0;">
                "Purity in every smile"<br>
                <span style="font-size: 14px;">— The Seraph Promise</span>
            </p>

            <p><strong>We'll keep you updated!</strong></p>

            <p>Best regards,<br>The Seraph Team</p>
        </div>

        <div class="footer">
            <p>© 2025 Seraph. All rights reserved.</p>
            <p>You're receiving this email because you signed up for the Seraph waitlist.</p>
        </div>
    </div>
</body>
</html>
HTML;

    $altBody = "Welcome to the Seraph Waitlist!\n\nHi " . $name . ",\n\nThank you for joining the Seraph waitlist!\n\nAs a waitlist member, you'll receive:\n- Exclusive launch pricing for early waitlist members\n- First access when we launch\n- Updates on our natural oral care journey\n- Tips for a healthier smile\n\nBest regards,\nThe Seraph Team";

    $result = sendEmail($email, $subject, $body, $altBody);

    // Log result
    if (!$result['success']) {
        error_log('Failed to send welcome email to ' . $email . ': ' . $result['message']);
    }

    return $result['success'];
}

/**
 * Send admin notification email
 */
function sendAdminNotification($data) {
    $subject = 'New Waitlist Signup - ' . $data['name'];

    $phone = !empty($data['phone']) ? $data['phone'] : 'Not provided';
    $kingschat = !empty($data['kingschat_username']) ? $data['kingschat_username'] : 'Not provided';

    $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #D97706; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; }
        .info-row { padding: 10px; border-bottom: 1px solid #ddd; }
        .label { font-weight: bold; color: #92400E; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Waitlist Signup</h2>
        </div>

        <div class="content">
            <div class="info-row">
                <span class="label">Name:</span> {$data['name']}
            </div>
            <div class="info-row">
                <span class="label">Email:</span> {$data['email']}
            </div>
            <div class="info-row">
                <span class="label">City:</span> {$data['city']}
            </div>
            <div class="info-row">
                <span class="label">Phone:</span> {$phone}
            </div>
            <div class="info-row">
                <span class="label">KingsChat:</span> {$kingschat}
            </div>
            <div class="info-row">
                <span class="label">Time:</span> {$data['subscribed_at']}
            </div>
        </div>
    </div>
</body>
</html>
HTML;

    $altBody = "New Waitlist Signup\n\nName: " . $data['name'] . "\nEmail: " . $data['email'] . "\nCity: " . $data['city'] . "\nPhone: " . $phone . "\nKingsChat: " . $kingschat . "\nTime: " . $data['subscribed_at'];

    $result = sendEmail(ADMIN_EMAIL, $subject, $body, $altBody);

    // Log result
    if (!$result['success']) {
        error_log('Failed to send admin notification: ' . $result['message']);
    }

    return $result['success'];
}

/**
 * Send distributor application notification email
 */
function sendDistributorApplicationEmail($data) {
    $subject = 'New Distributor Application - ' . $data['full_name'];

    $businessType = !empty($data['business_type']) ? ucfirst($data['business_type']) : 'Not specified';
    $message = !empty($data['message']) ? nl2br(htmlspecialchars($data['message'])) : 'No message provided';

    $body = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; line-height: 1.6; color: #2D2A26; background: #FAF6F1; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2D2A26; color: #FAF6F1; padding: 30px; text-align: center; }
        .logo { font-family: 'Playfair Display', Georgia, serif; font-size: 28px; letter-spacing: 0.3em; color: #FAF6F1; margin-bottom: 5px; }
        .tagline { color: #C8956C; font-size: 14px; }
        .content { background: white; padding: 30px; border: 1px solid rgba(45, 42, 38, 0.1); }
        .section-title { color: #C8956C; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 15px; font-weight: 600; }
        .info-row { padding: 12px 0; border-bottom: 1px solid #FAF6F1; display: flex; }
        .info-row:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #51504A; min-width: 130px; }
        .value { color: #2D2A26; }
        .message-box { background: #FAF6F1; padding: 20px; margin-top: 20px; border-left: 3px solid #C8956C; }
        .footer { text-align: center; padding: 20px; color: #8A8880; font-size: 12px; }
        .badge { display: inline-block; background: #C8956C; color: white; padding: 4px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">SERAPH</div>
            <p class="tagline">Distributor Application</p>
        </div>

        <div class="content">
            <span class="badge">New Application</span>

            <p class="section-title">Applicant Details</p>

            <div class="info-row">
                <span class="label">Full Name:</span>
                <span class="value">{$data['full_name']}</span>
            </div>
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">{$data['email']}</span>
            </div>
            <div class="info-row">
                <span class="label">Phone:</span>
                <span class="value">{$data['phone']}</span>
            </div>
            <div class="info-row">
                <span class="label">Location:</span>
                <span class="value">{$data['location']}</span>
            </div>
            <div class="info-row">
                <span class="label">Business Type:</span>
                <span class="value">{$businessType}</span>
            </div>
            <div class="info-row">
                <span class="label">Submitted:</span>
                <span class="value">{$data['submitted_at']}</span>
            </div>

            <div class="message-box">
                <p class="section-title">Message from Applicant</p>
                <p style="margin: 0; color: #51504A;">{$message}</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2025 Seraph. All rights reserved.</p>
            <p>This is an automated notification from your website.</p>
        </div>
    </div>
</body>
</html>
HTML;

    $altBody = "New Distributor Application\n\n" .
        "Full Name: " . $data['full_name'] . "\n" .
        "Email: " . $data['email'] . "\n" .
        "Phone: " . $data['phone'] . "\n" .
        "Location: " . $data['location'] . "\n" .
        "Business Type: " . $businessType . "\n" .
        "Submitted: " . $data['submitted_at'] . "\n\n" .
        "Message:\n" . ($data['message'] ?? 'No message provided');

    $result = sendEmail(ADMIN_EMAIL, $subject, $body, $altBody);

    // Log result
    if (!$result['success']) {
        error_log('Failed to send distributor application email: ' . $result['message']);
    }

    return $result['success'];
}
