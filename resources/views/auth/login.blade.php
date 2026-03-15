<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            background: #f1f3f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 2.25rem 2rem;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 16px rgba(0, 0, 0, .07);
        }

        h4 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: .3rem;
        }

        .subtitle {
            color: #6c757d;
            font-size: .85rem;
            margin-bottom: 1.5rem;
        }

        .demo-block {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-bottom: 1.5rem;
        }

        .demo-title {
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #9ca3af;
            margin-bottom: .6rem;
        }

        .demo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .demo-row .badge {
            font-size: .7rem;
            font-weight: 600;
            background: #fee2e2;
            color: #b91c1c;
            padding: .2rem .5rem;
            border-radius: 4px;
            margin-right: .35rem;
        }

        .demo-row code {
            font-size: .8rem;
            color: #374151;
            background: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            font-size: .8rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: .35rem;
        }

        input[type="email"], input[type="password"], input[type="text"] {
            display: block;
            width: 100%;
            padding: .55rem .75rem;
            font-size: .875rem;
            color: #1a1a2e;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            outline: none;
            transition: border-color .15s;
        }

        input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus {
            border-color: #7366ff;
            box-shadow: 0 0 0 3px rgba(115, 102, 255, .12);
        }

        input.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: .78rem;
            margin-top: .25rem;
        }

        .pwd-wrap {
            position: relative;
        }

        .toggle-pwd {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            color: #9ca3af;
            font-size: .75rem;
            font-weight: 500;
        }

        .toggle-pwd:hover {
            color: #374151;
        }

        .bottom-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .check-wrap {
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .check-wrap input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #7366ff;
            cursor: pointer;
        }

        .check-wrap label {
            margin: 0;
            font-size: .82rem;
            color: #6c757d;
            font-weight: 400;
            cursor: pointer;
        }

        .forgot {
            font-size: .82rem;
            color: #7366ff;
            text-decoration: none;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        .btn-submit {
            display: block;
            width: 100%;
            padding: .6rem;
            font-size: .9rem;
            font-weight: 500;
            color: #fff;
            background: #7366ff;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-submit:hover {
            background: #5c50e6;
        }

        .btn-submit:active {
            background: #4b40cc;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-radius: 7px;
            padding: .6rem .85rem;
            font-size: .83rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="card">
    <h4>Sign in</h4>
    <p class="subtitle">Enter your email &amp; password to continue</p>

    <div class="demo-block">
        <div class="demo-title">Demo Credentials</div>
        <div class="demo-row">
            <div>
                <span class="badge">Admin</span>
                <code>admin@logiatech.com</code>
            </div>
            <div>
                <span class="badge">Password</span>
                <code>password</code>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="you@example.com"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                   required autofocus autocomplete="username">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="pwd">Password</label>
            <div class="pwd-wrap">
                <input id="pwd" type="password" name="password"
                       placeholder="••••••••"
                       class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                       required autocomplete="current-password">
                <button type="button" class="toggle-pwd" onclick="togglePwd(this)">Show</button>
            </div>
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="bottom-row">
            <div class="check-wrap">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit">Sign in</button>
    </form>
</div>

<script>
    function togglePwd(btn) {
        const input = document.getElementById('pwd');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.textContent = isHidden ? 'Hide' : 'Show';
    }
</script>

</body>
</html>
