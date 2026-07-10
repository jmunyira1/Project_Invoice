<x-guest-layout>
    <div class="text-center mb-3">
        <h4 class="fw-semibold mb-1">Sign in</h4>
        <p class="text-muted small mb-0">Enter your email &amp; password to continue</p>
    </div>

    <div class="alert alert-light border small py-2 mb-3">
        <div class="text-muted text-uppercase fw-semibold mb-1" style="font-size:.65rem;letter-spacing:.05em">
            Demo credentials
        </div>
        <div class="d-flex justify-content-between">
            <span><span class="badge badge-soft-danger me-1">Admin</span><code>admin@demo.com</code></span>
            <span><span class="badge badge-soft-danger me-1">Pass</span><code>20252025</code></span>
        </div>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-medium">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="you@example.com" required autofocus autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-medium">Password</label>
            <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••" required autocomplete="current-password">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePwd(this)">
                    <i class="bi bi-eye"></i>
                </button>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label small text-muted" for="remember">Remember me</label>
            </div>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign in</button>
    </form>

    <script>
        function togglePwd(btn) {
            const input = document.getElementById('password');
            const hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            btn.querySelector('i').className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        }
    </script>
</x-guest-layout>
