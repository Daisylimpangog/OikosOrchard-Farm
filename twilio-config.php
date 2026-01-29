<?php
// Twilio SMS Configuration
// Credentials for sending SMS notifications

define('TWILIO_ACCOUNT_SID', 'AC908fe3981a786a70aa5abef83846c6ae');
define('TWILIO_AUTH_TOKEN', '3d39e6b53367beafca0d33d7ef47658f');

// Messaging Service (for sending SMS without phone number restrictions)
define('TWILIO_MESSAGING_SERVICE_SID', 'MG3ca82a9dcf1f50b54ad023ca49e8b9');

// Recipient phone number for notifications
define('NOTIFY_PHONE_NUMBER', '+639948962820'); // Your Philippines number

// SMS Messages
define('BOOKING_RECEIVED_SUBJECT', 'New Booking Notification');
define('GETSTARTED_RECEIVED_SUBJECT', 'New Get Started Inquiry');
?>
