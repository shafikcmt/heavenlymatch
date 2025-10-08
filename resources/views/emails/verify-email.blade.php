<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            color: #ffffff;
            text-align: center;
            padding: 30px;
            font-size: 24px;
            font-weight: bold;
        }
        .tagline {
            font-size: 14px;
            color: #e3f2fd;
            margin-top: 8px;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .content h2 {
            color: #333333;
        }
        .code-box {
            display: inline-block;
            margin: 20px 0;
            padding: 15px 25px;
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            border: 2px dashed #007bff;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 25px;
            background-color: #28a745;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
        }
        .button:hover {
            background-color: #218838;
        }
        .footer {
            background-color: #f1f3f5;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #777777;
        }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            HeavenlyMatch 💌
            <div class="tagline">Your journey to meaningful connections starts here 💖</div>
        </div>
        <div class="content">
            <h2>Hello, {{ $name }}!</h2>
            <p>Thank you for joining <strong>HeavenlyMatch</strong>! We're delighted to have you on board.</p>
            
            <p>Please use the code below to verify your email:</p>
            <div class="code-box">
                {{ $code }}
            </div>

            <p>Or click the button below to verify instantly:</p>
            <a href="{{ url('/verify-email/'.$token) }}" class="button">Verify Email</a>

            <p>We are thrilled to help you begin your journey with us.<br>
            Thank you for trusting <strong>HeavenlyMatch</strong> 💕</p>

            <p>If you did not sign up, please ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ now()->year }} HeavenlyMatch. All rights reserved.
        </div>
    </div>
</body>
</html>
