<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            border-bottom: 3px solid #007bff;
        }
        .content {
            padding: 20px 0;
        }
        .field {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .field:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            color: #333;
            word-break: break-word;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #999;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; color: #333;">New Contact Form Submission</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="label">Name:</span>
                <span class="value">{{ $name }}</span>
            </div>
            
            <div class="field">
                <span class="label">Email:</span>
                <span class="value">{{ $email }}</span>
            </div>
            
            @if($phone)
            <div class="field">
                <span class="label">Phone:</span>
                <span class="value">{{ $phone }}</span>
            </div>
            @endif
            
            <div class="field">
                <span class="label">Message:</span>
                <span class="value">{{ $comment }}</span>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message from your website contact form.</p>
        </div>
    </div>
</body>
</html>
