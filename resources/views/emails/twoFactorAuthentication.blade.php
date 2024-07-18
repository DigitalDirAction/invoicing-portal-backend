<!-- resources/views/emails/twoFactorAuth.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .content {
            margin: 20px 0;
            text-align: center;
        }

        .code {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }

        .footer {
            text-align: center;
            color: #777;
            font-size: 12px;
            margin-top: 20px;
        }

        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Two-Factor Authentication Code</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user['name'] }},</p>
            <p>Your two-factor authentication code is:</p>
            <p class="code"><strong>{{ $token }}</strong></p>
            <p>Please enter this code to complete your sign-in process.</p>
            <p>If you did not request this code, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>Thank you,</p>
            <p>The Invoicing Portal Team</p>
        </div>
    </div>
</body>

</html>