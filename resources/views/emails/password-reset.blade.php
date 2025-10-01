<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - PSU Dorm Finder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transition: transform 0.2s;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        .alternative-link {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .alternative-link p {
            font-size: 13px;
            color: #777;
            margin-bottom: 10px;
        }
        .alternative-link a {
            color: #667eea;
            word-break: break-all;
            font-size: 12px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #888;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .security-notice p {
            margin: 0;
            font-size: 13px;
            color: #856404;
        }
        .security-notice strong {
            color: #664d03;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
            <p>PSU Dorm Finder</p>
        </div>
        <div class="content">
            <div class="greeting">
                Hello{{ isset($user) && $user->name ? ', ' . $user->name : '' }}!
            </div>
            <div class="message">
                <p>You are receiving this email because we received a password reset request for your PSU Dorm Finder account.</p>
                <p>To reset your password, click the button below:</p>
            </div>
            <div class="button-container">
                <a href="{{ $actionUrl }}" class="button">Reset Password</a>
            </div>
            <div class="security-notice">
                <p><strong>⏰ Important:</strong> This password reset link will expire in <strong>60 minutes</strong> for security reasons.</p>
            </div>
            <div class="info-box">
                <p><strong>💡 Didn't request a password reset?</strong></p>
                <p>If you did not request a password reset, no further action is required. Your password will remain unchanged and your account is secure.</p>
            </div>
            <div class="alternative-link">
                <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
                <a href="{{ $actionUrl }}">{{ $actionUrl }}</a>
            </div>
        </div>
        <div class="footer">
            <p><strong>PSU Dorm Finder</strong></p>
            <p>Finding your perfect dorm near Pampanga State Agricultural University</p>
            <p style="margin-top: 15px;">
                <a href="{{ url('/') }}">Visit Website</a> |
                <a href="{{ url('/about') }}">About Us</a> |
                <a href="{{ url('/how-it-works') }}">How It Works</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{ date('Y') }} PSU Dorm Finder. All rights reserved.
            </p>
            <p style="font-size: 11px; color: #aaa;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
