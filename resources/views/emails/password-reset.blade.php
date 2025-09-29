<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset - PSU Dorm Finder</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            color: #10b981;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .reset-button {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .reset-button:hover {
            background: #059669;
        }
        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .link-fallback {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏠 PSU Dorm Finder</h1>
        </div>

        <div class="content">
            <h2>Reset Your Password</h2>

            <p>Hello!</p>

            <p>You are receiving this email because we received a password reset request for your PSU Dorm Finder account.</p>

            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="reset-button">Reset Password</a>
            </div>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong> This password reset link will expire in {{ config('auth.passwords.users.expire') }} minutes for your security.
            </div>

            <p>If you did not request a password reset, no further action is required. Your current password will remain unchanged.</p>

            <p>If you're having trouble clicking the button above, copy and paste the URL below into your web browser:</p>

            <div class="link-fallback">
                {{ $actionUrl }}
            </div>
        </div>

        <div class="footer">
            <p>This email was sent by PSU Dorm Finder</p>
            <p>If you have any questions, please contact your system administrator.</p>
            <p>&copy; {{ date('Y') }} Pampanga State University. All rights reserved.</p>
        </div>
    </div>
</body>
</html>