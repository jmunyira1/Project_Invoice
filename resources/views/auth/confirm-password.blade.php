<x-guest-layout>
    <div class="text-center mb-3">
        <h4 class="fw-semibold mb-1">Confirm password</h4>
    </div>

    <p class="text-muted small mb-3">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('Confirm') }}</button>
    </form>
</x-guest-layout>
