<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Email Verification</h1>
        <p>Hi {{ $user->name }},</p>
        <p>Thank you for registering on Invoicing Portal. Please click the button below to verify your email address:
        </p>
        <a href="{{ $url }}" class="button">Verify Email</a>
        <p>If you did not create an account, no further action is required.</p>
        <p>Regards,<br>Team Invoicing Portal</p>
    </div>
</body>

</html>