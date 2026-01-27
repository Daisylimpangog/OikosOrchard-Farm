const https = require('https');

// Google Apps Script webhook URL
const WEBHOOK_URL = 'https://script.google.com/macros/s/AKfycbyfgMWh3i6EvBrf6yyNkrHsX7LFUYXTvzZ3C95oEI7DVcDOmWLXOUdj1j4PMbag_-fI7w/exec';

exports.handler = async (event, context) => {
  // Only allow POST requests
  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      body: JSON.stringify({ success: false, message: 'Method not allowed' })
    };
  }

  try {
    // Parse incoming data
    const data = JSON.parse(event.body);

    // Validate required fields
    if (!data.fullName || !data.email || !data.phone || !data.checkinDate || !data.guests || !data.packageName) {
      return {
        statusCode: 400,
        body: JSON.stringify({ success: false, message: 'Please fill all required fields' })
      };
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
      return {
        statusCode: 400,
        body: JSON.stringify({ success: false, message: 'Invalid email address' })
      };
    }

    // Prepare booking data
    const bookingData = {
      fullName: data.fullName,
      email: data.email,
      phone: data.phone,
      checkinDate: data.checkinDate,
      guests: data.guests,
      packageName: data.packageName,
      packagePrice: data.packagePrice || '',
      specialRequests: data.specialRequests || '',
      timestamp: new Date().toLocaleString(),
      bookingId: 'booking_' + Date.now()
    };

    // Send to Google Apps Script
    await sendToGoogleSheets(bookingData);

    // Send confirmation email (optional - can be implemented with SendGrid, Mailgun, etc.)
    // await sendConfirmationEmail(bookingData);

    return {
      statusCode: 200,
      body: JSON.stringify({
        success: true,
        message: 'Booking submitted successfully! A confirmation email has been sent to ' + data.email + '. Our team will contact you within 24 hours at ' + data.phone + '.',
        data: bookingData
      })
    };

  } catch (error) {
    console.error('Error processing booking:', error);
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Server error: ' + error.message })
    };
  }
};

// Function to send data to Google Sheets via Apps Script
function sendToGoogleSheets(bookingData) {
  return new Promise((resolve, reject) => {
    const payload = JSON.stringify(bookingData);

    const options = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Content-Length': payload.length
      }
    };

    const req = https.request(WEBHOOK_URL, options, (res) => {
      let responseBody = '';

      res.on('data', (chunk) => {
        responseBody += chunk;
      });

      res.on('end', () => {
        if (res.statusCode >= 200 && res.statusCode < 300) {
          console.log('Successfully sent to Google Sheets:', bookingData.bookingId);
          resolve(responseBody);
        } else {
          console.error('Failed to send to Google Sheets. Status:', res.statusCode);
          reject(new Error('Failed to send to Google Sheets: HTTP ' + res.statusCode));
        }
      });
    });

    req.on('error', (error) => {
      console.error('Error sending to Google Sheets:', error);
      reject(error);
    });

    req.write(payload);
    req.end();
  });
}
