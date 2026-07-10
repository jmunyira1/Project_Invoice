<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Sign in')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="login-page bg-body-secondary">
<div class="login-box">
    <div class="login-logo mb-2">
        <a href="{{ url('/') }}" class="text-decoration-none">
            <i class="bi bi-receipt-cutoff me-1"></i>
            <b>{{ config('app.name') }}</b>
        </a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body login-card-body">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
