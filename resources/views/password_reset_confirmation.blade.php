<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #EFFEF8;
            color: #087D7B;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #C9FEF6;
            border-radius: 8px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        h3 {
            color: #0F5252;
        }
        .btn {
            display: inline-block;
            background-color: #09C3BA;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #087D7B;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Hello,</h3>
        <p>We received a request to reset your password. If you made this request, click the button below to confirm:</p>

        <!-- Form with submit button -->
        <form id="resetForm" action="{{ $resetLink }}" method="POST">
            <input type="hidden" name="token" value="{{ $token }}">
            <button type="submit" class="btn">Yes, reset my password</button>
        </form>

        <p>If you did not request this change, you can safely ignore this email.</p>
    </div>
</body>
</html>
