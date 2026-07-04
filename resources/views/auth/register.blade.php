<x-guest-layout>
    <div class="mb-6">
        <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-muted">Get started</div>
        <h2 class="mt-2 font-heading text-[28px] font-extrabold text-text">Create Account</h2>
        <p class="mt-2 text-[13px] text-muted">Register to buy and download {{ config('app.name') }} products.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full py-3">
            {{ __('Register') }}
        </x-primary-button>
    </form>

    <div class="mt-6 flex items-center justify-between text-sm text-muted">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-semibold text-text"><span class="icon" data-icon="arrow-left"></span>Back to store</a>
        <a href="{{ route('login') }}" class="font-semibold text-accent">{{ __('Already registered?') }}</a>
    </div>
</x-guest-layout>
