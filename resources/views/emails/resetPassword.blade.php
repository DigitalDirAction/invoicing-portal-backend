<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
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
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .content {
            margin: 20px 0;
            text-align: center;
        }

        .button {
            display: inline-block;
            background-color: #007BFF;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none !important;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            color: #777;
            font-size: 12px;
            margin-top: 20px;
        }

        .footer a {
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        <div class="content">
            <p>We received a request to reset your password. Click the button below to reset your password:</p>
            <a href="{{ url('password/reset', $token) }}" class="button">Reset Password</a>
            <p>If you did not request a password reset, please ignore this email or contact support if you have
                questions.</p>
        </div>
        <div class="footer">
            <p>Thank you,</p>
            <p>The Invoicing Portal Team</p>
        </div>
    </div>
</body>

</html>