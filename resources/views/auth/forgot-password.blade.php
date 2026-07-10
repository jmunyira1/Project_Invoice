<x-guest-layout>
    <div class="text-center mb-3">
        <h4 class="fw-semibold mb-1">Forgot password?</h4>
        <p class="text-muted small mb-0">Enter your email and we'll send you a reset link.</p>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')"
                          placeholder="you@example.com" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <button type="submit" class="btn btn-primary w-100">Send reset link</button>

        <p class="text-center small mt-3 mb-0">
            <a href="{{ route('login') }}" class="text-decoration-none">Back to sign in</a>
        </p>
    </form>
</x-guest-layout>
