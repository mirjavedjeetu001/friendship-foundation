<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            color: #374151;
            line-height: 1.6;
        }
        .content h2 {
            color: #1f2937;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .info-box {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
        }
        .otp-box {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            padding: 20px 40px;
            border-radius: 10px;
            display: inline-block;
            margin: 20px 0;
        }
        .footer {
            background-color: #1f2937;
            padding: 25px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }
        .footer a {
            color: #818cf8;
            text-decoration: none;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .success {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    @php
        $appSettings = \App\Models\MonthlySetting::getSettings();
        $logoUrl = $appSettings->logo ? url('storage/' . $appSettings->logo) : null;
    @endphp
    <div class="email-wrapper">
        <div class="header">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ config('app.name', 'Allied Group') }}" style="max-height: 60px; max-width: 120px; margin-bottom: 10px;">
            @endif
            <h1>{{ config('app.name', 'Allied Group') }}</h1>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Allied Group') }}. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
