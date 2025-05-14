<x-guest-layout>
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
        <div class="mb-6 text-center">
            <a href="/">
                <img src="{{ asset('images/vista.png') }}" alt="Vista Logo" class="mx-auto w-20 h-auto" />
            </a>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                    :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                    required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-between mt-4">
                <a href="{{ route('register') }}"
                    class="text-sm text-gray-600 hover:text-gray-900">
                    ← First Time User
                </a>

                <div class="flex items-center gap-2">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900"
                            href="{{ route('password.request') }}">
                            Forgot?
                        </a>
                    @endif

                    <x-primary-button>
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
