<?php
header('Content-Type: application/json; charset=utf-8');

// Load configuration
require_once __DIR__ . '/gmail-config.php';

// Load PHPMailer
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Get JSON data from request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data received']);
        exit;
    }

    // Validate required fields
    if (empty($input['name']) || empty($input['email']) || empty($input['phone']) || empty($input['interested'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }

    // Sanitize inputs
    $name = htmlspecialchars(trim($input['name']));
    $email = htmlspecialchars(trim($input['email']));
    $phone = htmlspecialchars(trim($input['phone']));
    $interested = htmlspecialchars(trim($input['interested']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }

    // Initialize PHPMailer
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->SMTPAuth = SMTP_AUTH;
    $mail->Username = GMAIL_ADDRESS;
    $mail->Password = GMAIL_APP_PASSWORD;
    $mail->From = GMAIL_ADDRESS;
    $mail->FromName = MAIL_FROM_NAME;
    $mail->isHTML = true;
    $mail->CharSet = 'UTF-8';

    // ====== SEND EMAIL TO ADMIN ======
    $adminSubject = 'New Get Started Request - Oikos Orchard & Farm';
    $adminMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; border-radius: 8px; }
            .header { background: #27ae60; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { background: white; padding: 20px; }
            .field { margin: 15px 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
            .label { font-weight: bold; color: #27ae60; display: inline-block; width: 150px; }
            .value { display: inline-block; }
            .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>📋 New Get Started Request</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Name:</span>
                    <span class='value'>{$name}</span>
                </div>
                <div class='field'>
                    <span class='label'>Email:</span>
                    <span class='value'>{$email}</span>
                </div>
                <div class='field'>
                    <span class='label'>Phone:</span>
                    <span class='value'>{$phone}</span>
                </div>
                <div class='field'>
                    <span class='label'>Interested In:</span>
                    <span class='value'>{$interested}</span>
                </div>
                <div class='field'>
                    <span class='label'>Submitted:</span>
                    <span class='value'>" . date('Y-m-d H:i:s') . "</span>
                </div>
            </div>
            <div class='footer'>
                <p>This is an automated notification from Oikos Orchard & Farm website.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Subject = $adminSubject;
    $mail->Body = $adminMessage;
    $mail->addAddress(ADMIN_EMAIL, 'Oikos Admin');
    
    $adminEmailSent = false;
    try {
        $adminEmailSent = $mail->send();
    } catch (Exception $e) {
        error_log("Admin email error: " . $e->getMessage());
    }

    // ====== SEND CONFIRMATION EMAIL TO USER ======
    $userSubject = 'Thank You for Getting Started - Oikos Orchard & Farm';
    $userMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; border-radius: 8px; }
            .header { background: #27ae60; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { background: white; padding: 20px; line-height: 1.6; }
            .button { display: inline-block; background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🌱 Welcome to Oikos Orchard & Farm!</h2>
            </div>
            <div class='content'>
                <p>Dear <strong>{$name}</strong>,</p>
                <p>Thank you for your interest in <strong>Oikos Orchard & Farm</strong>! We have received your request and will contact you shortly.</p>
                
                <h3>Your Request Details:</h3>
                <ul>
                    <li><strong>Email:</strong> {$email}</li>
                    <li><strong>Phone:</strong> {$phone}</li>
                    <li><strong>Interested In:</strong> {$interested}</li>
                </ul>

                <p>Our team will reach out to you within <strong>24 hours</strong> to discuss your needs and how we can help you.</p>

                <h3>Contact Information:</h3>
                <ul>
                    <li>📧 <strong>Email:</strong> oikosorchardandfarm2@gmail.com</li>
                    <li>📱 <strong>Phone:</strong> +63 917 777 0851</li>
                    <li>📍 <strong>Address:</strong> Vegetable Highway, Upper Bae, Sibonga, Cebu, Philippines</li>
                </ul>

                <p>If you have any immediate questions, feel free to reach out to us directly.</p>

                <p>Best regards,<br>
                <strong>🌿 Oikos Orchard & Farm Team</strong></p>
            </div>
            <div class='footer'>
                <p>&copy; 2026 Oikos Orchard & Farm. All rights reserved.</p>
                <p>Sustainable Agriculture | Organic Products | Agritourism</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Clear previous recipients
    $mail->clearAllRecipients();
    
    $mail->Subject = $userSubject;
    $mail->Body = $userMessage;
    $mail->addAddress($email, $name);
    $mail->addReplyTo(GMAIL_ADDRESS, MAIL_FROM_NAME);

    $userEmailSent = false;
    try {
        $userEmailSent = $mail->send();
    } catch (Exception $e) {
        error_log("User email error: " . $e->getMessage());
    }

    // Respond based on email results
    if ($adminEmailSent && $userEmailSent) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! We have received your request and will contact you shortly. Check your email for confirmation.'
        ]);
    } else if ($adminEmailSent) {
        // Admin received it but user didn't
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Your request has been received! We will contact you soon.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error sending email. Please try again later or contact us directly.'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    error_log("Get Started error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error. Please try again later.'
    ]);
}
?>
