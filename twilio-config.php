<?php
// Twilio SMS Configuration
// Credentials for sending SMS notifications

define('TWILIO_ACCOUNT_SID', 'AC908fe39813768a70aaSabef83');
define('TWILIO_AUTH_TOKEN', '3d39e6b53367beafca0d33d7ef47658f');
define('TWILIO_PHONE_NUMBER', '+14437310255'); // Your Twilio phone number (sender)

// Recipient phone number for notifications
define('NOTIFY_PHONE_NUMBER', '+639948962820'); // Your Philippines number (09948962820 → +639948962820)

// SMS Messages
define('BOOKING_RECEIVED_SUBJECT', 'New Booking Notification');
define('GETSTARTED_RECEIVED_SUBJECT', 'New Get Started Inquiry');
?>
