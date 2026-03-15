<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — @yield('title', 'Login')</title>

    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
</head>
<body>
<div class="container-fluid p-0">
    <div class="row m-0">

        {{-- Left branding panel (hidden on mobile) --}}
        <div class="col-xl-7 d-none d-xl-block p-0">
            <div class="login-card1 h-100">
                <div class="logo-wrapper mb-4">
                    <a href="{{ url('/') }}">
                        <img class="img-fluid for-light"
                             src="{{ asset('assets/images/logo/logo.png') }}"
                             alt="{{ config('app.name') }}">
                    </a>
                </div>
                <img class="img-fluid for-light"
                     src="{{ asset('assets/images/login/1.jpg') }}"
                     alt="login illustration">
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="col-xl-5 col-lg-12 p-0">
            <div class="login-card login-dark">
                <div>
                    {{-- Mobile logo --}}
                    <div class="logo d-xl-none mb-4">
                        <a href="{{ url('/') }}">
                            <img class="img-fluid for-light"
                                 src="{{ asset('assets/images/logo/logo.png') }}"
                                 alt="{{ config('app.name') }}">
                        </a>
                    </div>

                    <div class="login-main">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
