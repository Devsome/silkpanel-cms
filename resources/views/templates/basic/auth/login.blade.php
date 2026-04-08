@extends('template::layouts.guest', ['title' => __('auth/login.form.title')])

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900 px-4 py-12">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}" alt=""
                class="absolute inset-0 h-full w-full object-cover opacity-15" />
        @endif

        <div class="relative w-full max-w-md">
            {{-- Logo / Branding --}}
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-block">
                    @if (\App\Helpers\SettingHelper::get('site_logo'))
                        <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('site_logo')) }}"
                            alt="{{ config('app.name') }}" class="h-12 mx-auto" />
                    @else
                        <span class="text-2xl font-bold text-white">{{ config('app.name') }}</span>
                    @endif
                </a>
                <h1 class="mt-6 text-3xl font-bold text-white">
                    {{ __('auth/login.form.title') }}
                </h1>
                <p class="mt-2 text-gray-400">
                    {{ __('auth/login.description') }}
                </p>
            </div>

            {{-- Login Card --}}
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8 shadow-2xl">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        @if (\App\Models\Setting::get('login_with_name', false))
                            <label for="email" class="block text-sm font-medium text-gray-200">
                                {{ __('auth/login.form.username_or_email') }}
                            </label>
                            <input id="email" name="email" type="text" value="{{ old('email') }}" required
                                autofocus autocomplete="username"
                                class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        @else
                            <label for="email" class="block text-sm font-medium text-gray-200">
                                {{ __('auth/login.form.email') }}
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                autofocus autocomplete="email"
                                class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        @endif
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-200">
                            {{ __('auth/login.form.password') }}
                        </label>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="size-4 rounded border-white/30 bg-white/10 text-indigo-500 focus:ring-indigo-500" />
                            <span class="ml-2 text-sm text-gray-300">
                                {{ __('auth/login.form.remember_me') }}
                            </span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-indigo-400 hover:text-indigo-300 transition">
                                {{ __('auth/login.form.forgot_password') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-lg shadow-indigo-500/25">
                        {{ __('auth/login.form.login') }}
                    </button>
                </form>
            </div>

            @settingsRegistrationOpen
                <p class="mt-8 text-center text-sm text-gray-400">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition">
                        {{ __('navigation.register') }}
                    </a>
                </p>
            @endsettingsRegistrationOpen
        </div>
    </div>
@endsection
