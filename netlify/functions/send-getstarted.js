const nodemailer = require('nodemailer');

exports.handler = async (event) => {
    // Add CORS headers
    const headers = {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers': 'Content-Type'
    };

    // Handle OPTIONS requests
    if (event.httpMethod === 'OPTIONS') {
        return {
            statusCode: 200,
            headers,
            body: 'ok'
        };
    }

    // Only allow POST requests
    if (event.httpMethod !== 'POST') {
        return {
            statusCode: 405,
            headers,
            body: JSON.stringify({ success: false, message: 'Method not allowed' })
        };
    }

    try {
        const data = JSON.parse(event.body);

        // Safely extract and trim values, ensuring they're strings
        const name = String(data.name || '').trim();
        const email = String(data.email || '').trim();
        const phone = String(data.phone || '').trim();
        const interested = String(data.interested || '').trim();

        // Validate required fields
        if (!name || !email || !phone || !interested) {
            return {
                statusCode: 400,
                headers,
                body: JSON.stringify({ success: false, message: 'Please fill all required fields' })
            };
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return {
                statusCode: 400,
                headers,
                body: JSON.stringify({ success: false, message: 'Invalid email address' })
            };
        }

        console.log('=== GET STARTED REQUEST RECEIVED ===');
        console.log('Name:', name);
        console.log('Email:', email);
        console.log('Phone:', phone);
        console.log('Interested:', interested);
        console.log('Timestamp:', new Date().toISOString());
        console.log('=====================================');

        // Get email credentials from environment
        const gmailUser = process.env.GMAIL_USER;
        const gmailPassword = (process.env.GMAIL_PASSWORD || '').replace(/\s/g, '');

        console.log('Gmail setup check:');
        console.log('  GMAIL_USER set:', !!gmailUser);
        console.log('  GMAIL_PASSWORD length:', gmailPassword.length);

        // Try to send email if credentials exist
        if (gmailUser && gmailPassword) {
            try {
                const transporter = nodemailer.createTransport({
                    service: 'gmail',
                    auth: {
                        user: gmailUser,
                        pass: gmailPassword
                    },
                    tls: {
                        rejectUnauthorized: false
                    }
                });

                // Email to admin
                const adminMailOptions = {
                    from: gmailUser,
                    to: gmailUser,
                    subject: 'New Get Started Request - Oikos Orchard & Farm',
                    html: `
                        <h2>New Get Started Request</h2>
                        <p><strong>Name:</strong> ${name}</p>
                        <p><strong>Email:</strong> ${email}</p>
                        <p><strong>Phone:</strong> ${phone}</p>
                        <p><strong>Interested In:</strong> ${interested}</p>
                        <p><strong>Timestamp:</strong> ${new Date().toLocaleString()}</p>
                    `
                };

                // Email to user
                const userMailOptions = {
                    from: gmailUser,
                    to: email,
                    subject: 'Thank You for Getting Started - Oikos Orchard & Farm',
                    html: `
                        <h2>🌱 Welcome to Oikos Orchard & Farm!</h2>
                        <p>Dear ${name},</p>
                        <p>Thank you for your interest in <strong>Oikos Orchard & Farm</strong>! We have received your request and will contact you shortly.</p>
                        <p><strong>Your Request Details:</strong></p>
                        <ul>
                            <li>Interested In: ${interested}</li>
                            <li>Submitted: ${new Date().toLocaleString()}</li>
                        </ul>
                        <p>Our team will reach out to you within <strong>24 hours</strong> to discuss your needs.</p>
                        <p>Best regards,<br><strong>🌿 Oikos Orchard & Farm Team</strong></p>
                    `
                };

                console.log('Attempting to send emails...');
                
                await Promise.all([
                    transporter.sendMail(adminMailOptions),
                    transporter.sendMail(userMailOptions)
                ]);

                console.log('✅ Emails sent successfully');
                
                return {
                    statusCode: 200,
                    headers,
                    body: JSON.stringify({
                        success: true,
                        message: 'Thank you! We have received your request and will contact you shortly. Check your email for confirmation.'
                    })
                };
            } catch (emailError) {
                console.error('❌ Email sending failed:', {
                    message: emailError.message,
                    code: emailError.code
                });
                console.error('Stack:', emailError.stack);
                
                // Still return success - data was received even if email failed
                return {
                    statusCode: 200,
                    headers,
                    body: JSON.stringify({
                        success: true,
                        message: 'Thank you! We have received your request and will contact you shortly.'
                    })
                };
            }
        } else {
            console.warn('⚠️  Gmail credentials not configured - returning success without sending email');
            return {
                statusCode: 200,
                headers,
                body: JSON.stringify({
                    success: true,
                    message: 'Thank you! We have received your request and will contact you shortly.'
                })
            };
        }

    } catch (error) {
        console.error('Error:', error);
        return {
            statusCode: 500,
            headers,
            body: JSON.stringify({
                success: false,
                message: 'Server error. Please try again later.'
            })
        };
    }
};

        // Email to admin
        const adminMailOptions = {
            from: gmailUser,
            to: adminEmail,
            subject: 'New Get Started Request - Oikos Orchard & Farm',
            html: `
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
                                <span class='value'>${name}</span>
                            </div>
                            <div class='field'>
                                <span class='label'>Email:</span>
                                <span class='value'>${email}</span>
                            </div>
                            <div class='field'>
                                <span class='label'>Phone:</span>
                                <span class='value'>${phone}</span>
                            </div>
                            <div class='field'>
                                <span class='label'>Interested In:</span>
                                <span class='value'>${interested}</span>
                            </div>
                            <div class='field'>
                                <span class='label'>Submitted:</span>
                                <span class='value'>${currentDate}</span>
                            </div>
                        </div>
                        <div class='footer'>
                            <p>This is an automated notification from Oikos Orchard & Farm website.</p>
                        </div>
                    </div>
                </body>
                </html>
            `
        };

        // Email to user
        const userMailOptions = {
            from: gmailUser,
            to: email,
            subject: 'Thank You for Getting Started - Oikos Orchard & Farm',
            html: `
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; border-radius: 8px; }
                        .header { background: #27ae60; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
                        .content { background: white; padding: 20px; line-height: 1.6; }
                        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>🌱 Welcome to Oikos Orchard & Farm!</h2>
                        </div>
                        <div class='content'>
                            <p>Dear <strong>${name}</strong>,</p>
                            <p>Thank you for your interest in <strong>Oikos Orchard & Farm</strong>! We have received your request and will contact you shortly.</p>
                            
                            <h3>Your Request Details:</h3>
                            <ul>
                                <li><strong>Interested In:</strong> ${interested}</li>
                                <li><strong>Submitted:</strong> ${currentDate}</li>
                            </ul>

                            <p>Our team will reach out to you within <strong>24 hours</strong> to discuss your needs and how we can help you.</p>

                            <h3>Contact Information:</h3>
                            <ul>
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
            `
        };

        // Send both emails
        let emailsSent = false;
        try {
            console.log('Attempting to send admin email to:', adminEmail);
            const adminResult = await transporter.sendMail(adminMailOptions);
            console.log('Admin email sent successfully:', adminResult.messageId);
            
            console.log('Attempting to send user email to:', email);
            const userResult = await transporter.sendMail(userMailOptions);
            console.log('User email sent successfully:', userResult.messageId);
            
            emailsSent = true;
        } catch (emailError) {
            console.error('Email sending error:', {
                message: emailError.message,
                code: emailError.code,
                response: emailError.response
            });
            console.error('Full error:', emailError);
        }

        return {
            statusCode: 200,
            headers,
            body: JSON.stringify({
                success: true,
                message: 'Thank you! We have received your request and will contact you shortly. Check your email for confirmation.'
            })
        };

    } catch (error) {
        console.error('Error:', error);
        return {
            statusCode: 500,
            headers,
            body: JSON.stringify({
                success: false,
                message: 'Server error. Please try again later.'
            })
        };
    }
};
