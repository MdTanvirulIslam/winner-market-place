<x-guest-layout>
    <div class="mb-6">
        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-muted">Welcome back</div>
        <h2 class="mt-2 font-heading text-[28px] font-extrabold text-text">Login</h2>
        <p class="mt-2 text-[13px] text-muted">Sign in to your {{ config('app.name') }} account.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="flex items-center gap-2 text-muted">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-border bg-input text-accent focus:ring-accent" name="remember">
                {{ __('Remember me') }}
            </label>
            @if (Route::has('password.request'))
                <a class="font-semibold text-accent" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full py-3">
            {{ __('Sign In') }}
        </x-primary-button>
    </form>

    @if (Route::has('register'))
        <div class="mt-6 flex items-center justify-between text-sm text-muted">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-semibold text-text"><span class="icon" data-icon="arrow-left"></span>Back to store</a>
            <a href="{{ route('register') }}" class="font-semibold text-accent">Create account</a>
        </div>
    @endif
</x-guest-layout>
