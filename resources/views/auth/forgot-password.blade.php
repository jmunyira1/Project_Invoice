<x-guest-layout>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <h4>Forgot password?</h4>
        <p class="f-light">Enter your email and we'll send you a reset link.</p>

        @if(session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="form-group">
            <label class="col-form-label">Email address</label>
            <input class="form-control @error('email') is-invalid @enderror"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="you@example.com"
                   required
                   autofocus>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3 mb-0">
            <button class="btn btn-primary w-100" type="submit">
                Send reset link
            </button>
        </div>

        <div class="mt-3 text-center">
            <a href="{{ route('login') }}" class="link">Back to sign in</a>
        </div>

    </form>

</x-guest-layout>
