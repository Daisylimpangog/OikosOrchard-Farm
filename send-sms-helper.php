<?php
// SMS Helper using Twilio
require_once __DIR__ . '/twilio-config.php';

/**
 * Send SMS via Twilio
 * @param string $message - SMS message text
 * @param string $recipientNumber - Phone number to send to (default: NOTIFY_PHONE_NUMBER)
 * @return array - ['success' => bool, 'message' => string, 'sid' => string]
 */
function sendSMSViaTwilio($message, $recipientNumber = NOTIFY_PHONE_NUMBER) {
    try {
        // Prepare the request
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . TWILIO_ACCOUNT_SID . '/Messages.json';
        
        // Prepare data
        $postData = http_build_query([
            'From' => TWILIO_PHONE_NUMBER,
            'To' => $recipientNumber,
            'Body' => $message
        ]);
        
        // Create CURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_USERPWD, TWILIO_ACCOUNT_SID . ':' . TWILIO_AUTH_TOKEN);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Parse response
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300 && isset($responseData['sid'])) {
            return [
                'success' => true,
                'message' => 'SMS sent successfully',
                'sid' => $responseData['sid']
            ];
        } else {
            $errorMsg = $responseData['message'] ?? 'Unknown error';
            error_log("Twilio SMS Error: " . $errorMsg);
            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $errorMsg,
                'sid' => null
            ];
        }
        
    } catch (Exception $e) {
        error_log("Twilio Exception: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage(),
            'sid' => null
        ];
    }
}

/**
 * Send booking notification SMS
 * @param string $fullName - Customer name
 * @param string $email - Customer email
 * @param string $phone - Customer phone
 * @param string $packageName - Package name
 * @param string $checkinDate - Check-in date
 * @param string $guests - Number of guests
 * @return array - Result array
 */
function sendBookingSMS($fullName, $email, $phone, $packageName, $checkinDate, $guests) {
    $message = "🏕️ NEW BOOKING\n";
    $message .= "Name: $fullName\n";
    $message .= "Package: $packageName\n";
    $message .= "Check-in: $checkinDate\n";
    $message .= "Guests: $guests\n";
    $message .= "Phone: $phone\n";
    $message .= "Email: $email\n";
    $message .= "---\n";
    $message .= "Contact them to confirm!";
    
    return sendSMSViaTwilio($message);
}

/**
 * Send get-started inquiry notification SMS
 * @param string $name - Inquirer name
 * @param string $email - Inquirer email
 * @param string $phone - Inquirer phone
 * @param string $interested - What they're interested in
 * @return array - Result array
 */
function sendGetStartedSMS($name, $email, $phone, $interested) {
    $message = "📋 NEW INQUIRY\n";
    $message .= "Name: $name\n";
    $message .= "Interested: $interested\n";
    $message .= "Phone: $phone\n";
    $message .= "Email: $email\n";
    $message .= "---\n";
    $message .= "Follow up within 24 hours!";
    
    return sendSMSViaTwilio($message);
}

/**
 * Send customer confirmation SMS
 * @param string $name - Customer name
 * @param string $customerPhone - Customer phone number (with country code)
 * @param string $type - Type: 'booking' or 'inquiry'
 * @return array - Result array
 */
function sendCustomerConfirmationSMS($name, $customerPhone, $type = 'booking') {
    if ($type === 'booking') {
        $message = "✓ Booking received!\n";
        $message .= "Hi $name, thank you for your booking request.\n";
        $message .= "Our team will contact you within 24 hours.\n";
        $message .= "Oikos Orchard & Farm";
    } else {
        $message = "✓ Request received!\n";
        $message .= "Hi $name, thank you for your interest.\n";
        $message .= "We'll contact you shortly.\n";
        $message .= "Oikos Orchard & Farm";
    }
    
    return sendSMSViaTwilio($message, $customerPhone);
}

?>
