<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portal Catering') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f8f9fa;
            }
            .auth-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #4CAF50 0%, #A5D6A7 100%);
                padding: 40px 0;
            }
            .auth-card {
                width: 100%;
                max-width: 450px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
                overflow: hidden;
                position: relative;
                margin: 40px 0;
            }
            .auth-header {
                text-align: center;
                padding: 30px 0 20px;
            }
            .auth-header img {
                height: 60px;
                margin-bottom: 10px;
            }
            .auth-header h3 {
                font-size: 24px;
                font-weight: 600;
                color: #333;
            }
            .auth-body {
                padding: 20px 40px 40px;
            }
            .form-control {
                background-color: #f8f9fa;
                border: 1px solid #e4e6eb;
                border-radius: 8px;
                padding: 12px 15px;
                transition: all 0.3s;
            }
            .form-control:focus {
                border-color: #4CAF50;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
            }
            .btn-primary {
                background: #4CAF50;
                border: none;
                border-radius: 8px;
                padding: 12px 15px;
                font-weight: 500;
                letter-spacing: 0.5px;
                transition: all 0.3s;
                color: white;
            }
            .btn-primary:hover {
                background: #45a049;
                transform: translateY(-2px);
                box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
            }
            .auth-footer {
                text-align: center;
                padding: 20px;
                color: #6c757d;
                font-size: 14px;
            }
            .auth-link {
                color: #4CAF50;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s;
            }
            .auth-link:hover {
                color: #45a049;
            }
            .portal-link {
                color: #4CAF50;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
            }
            .portal-link:hover {
                color: #45a049;
                text-decoration: underline;
            }
            .input-group {
                position: relative;
                margin-bottom: 20px;
            }
            .input-icon {
                position: absolute;
                top: 50%;
                left: 15px;
                transform: translateY(-50%);
                color: #A5D6A7;
            }
            .input-with-icon {
                padding-left: 45px;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-card">
                @yield('content')
                
                <div class="auth-footer">
                    &copy; {{ date('Y') }} Portal Catering. All rights reserved.
                </div>
            </div>
        </div>
    </body>
</html>
