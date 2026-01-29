<?php
/**
 * Twilio SMS Test Tool
 * Test your SMS notification system
 * 
 * Visit: http://localhost/OikosOrchardandFarm/test-sms.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/send-sms-helper.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>SMS Notification Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .test { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .pass { background: #d4edda; border-color: #28a745; color: #155724; }
        .fail { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border-color: #ffc107; color: #856404; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        h1 { color: #333; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #0056b3; }
        input { padding: 8px; margin: 5px; width: 300px; border: 1px solid #ccc; border-radius: 3px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
    </style>
</head>
<body>

<h1>📱 SMS Notification Test</h1>

<div class="test info">
    <h3>Twilio Configuration</h3>
    <p><strong>Account SID:</strong> <code><?php echo substr(TWILIO_ACCOUNT_SID, 0, 10) . '...'; ?></code></p>
    <p><strong>Sender Phone:</strong> <code><?php echo TWILIO_PHONE_NUMBER; ?></code></p>
    <p><strong>Admin Recipient:</strong> <code><?php echo NOTIFY_PHONE_NUMBER; ?></code></p>
    <p><strong>Status:</strong> <span class="pass">✅ Configured</span></p>
</div>

<div class="test">
    <h3>Test 1: Send Test SMS to Admin</h3>
    <p>Send a simple test message to your admin number (<?php echo NOTIFY_PHONE_NUMBER; ?>)</p>
    <form method="POST">
        <button type="submit" name="test_admin" style="background: #27ae60;">📤 Send Test SMS</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_admin'])) {
        $testMessage = "🧪 Test SMS from Oikos Orchard & Farm\n\nIf you received this, SMS notifications are working!";
        $result = sendSMSViaTwilio($testMessage);
        
        if ($result['success']) {
            echo '<p class="pass">✅ SMS sent successfully!</p>';
            echo '<p>Message SID: <code>' . $result['sid'] . '</code></p>';
            echo '<p>Check your phone for the message at <code>' . NOTIFY_PHONE_NUMBER . '</code></p>';
        } else {
            echo '<p class="fail">❌ Failed to send SMS</p>';
            echo '<p>Error: ' . $result['message'] . '</p>';
        }
    }
    ?>
</div>

<div class="test">
    <h3>Test 2: Send Booking Notification</h3>
    <p>Simulate a booking notification</p>
    <form method="POST">
        <div class="form-group">
            <label>Customer Name:</label>
            <input type="text" name="booking_name" value="Juan Dela Cruz" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="booking_email" value="juan@example.com" required>
        </div>
        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="booking_phone" value="09123456789" required>
        </div>
        <div class="form-group">
            <label>Package:</label>
            <input type="text" name="booking_package" value="Premium Glamping" required>
        </div>
        <div class="form-group">
            <label>Check-in Date:</label>
            <input type="date" name="booking_checkin" required>
        </div>
        <div class="form-group">
            <label>Number of Guests:</label>
            <input type="number" name="booking_guests" value="4" required>
        </div>
        <button type="submit" name="test_booking" style="background: #27ae60;">📤 Send Booking SMS</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_booking'])) {
        $result = sendBookingSMS(
            htmlspecialchars($_POST['booking_name']),
            htmlspecialchars($_POST['booking_email']),
            htmlspecialchars($_POST['booking_phone']),
            htmlspecialchars($_POST['booking_package']),
            htmlspecialchars($_POST['booking_checkin']),
            htmlspecialchars($_POST['booking_guests'])
        );
        
        if ($result['success']) {
            echo '<p class="pass">✅ Booking SMS sent successfully!</p>';
            echo '<p>Message SID: <code>' . $result['sid'] . '</code></p>';
        } else {
            echo '<p class="fail">❌ Failed to send booking SMS</p>';
            echo '<p>Error: ' . $result['message'] . '</p>';
        }
    }
    ?>
</div>

<div class="test">
    <h3>Test 3: Send Inquiry Notification</h3>
    <p>Simulate a get-started inquiry notification</p>
    <form method="POST">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="inquiry_name" value="Maria Santos" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="inquiry_email" value="maria@example.com" required>
        </div>
        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="inquiry_phone" value="09234567890" required>
        </div>
        <div class="form-group">
            <label>Interested In:</label>
            <input type="text" name="inquiry_interested" value="Organic Products" required>
        </div>
        <button type="submit" name="test_inquiry" style="background: #27ae60;">📤 Send Inquiry SMS</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_inquiry'])) {
        $result = sendGetStartedSMS(
            htmlspecialchars($_POST['inquiry_name']),
            htmlspecialchars($_POST['inquiry_email']),
            htmlspecialchars($_POST['inquiry_phone']),
            htmlspecialchars($_POST['inquiry_interested'])
        );
        
        if ($result['success']) {
            echo '<p class="pass">✅ Inquiry SMS sent successfully!</p>';
            echo '<p>Message SID: <code>' . $result['sid'] . '</code></p>';
        } else {
            echo '<p class="fail">❌ Failed to send inquiry SMS</p>';
            echo '<p>Error: ' . $result['message'] . '</p>';
        }
    }
    ?>
</div>

<div class="test warning">
    <h3>⚠️ Important</h3>
    <ul>
        <li>This test file is for development only</li>
        <li><strong>Delete this file</strong> before deploying to production</li>
        <li>File to delete: <code>test-sms.php</code></li>
        <li>Your Twilio credentials are stored in <code>twilio-config.php</code> - keep it secure</li>
        <li>Add <code>twilio-config.php</code> to <code>.gitignore</code> if using GitHub</li>
    </ul>
</div>

<div class="test info">
    <h3>📋 Next Steps</h3>
    <ol>
        <li>Run Test 1 above to verify SMS connection works</li>
        <li>Check your phone (<?php echo NOTIFY_PHONE_NUMBER; ?>) for the test message</li>
        <li>If message arrives → SMS system is working! ✅</li>
        <li>Test your booking form at <code>/Offers.html</code></li>
        <li>Test your get-started form at <code>/index.html</code></li>
        <li>You should receive SMS notifications on your phone</li>
    </ol>
</div>

</body>
</html>
