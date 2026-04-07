@extends('template::layouts.guest', ['title' => __('Log in')])

@section('content')
<div class="min-h-screen flex">
    {{-- Left Panel - Image --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}"
                 alt="" class="absolute inset-0 h-full w-full object-cover opacity-60" />
        @endif
        <div class="relative flex flex-col justify-end p-12">
            <h2 class="text-3xl font-bold text-white">
                {{ __('auth/login.title', ['app_name' => config('app.name')]) }}
            </h2>
            <p class="mt-4 text-lg text-gray-300">
                {{ __('auth/login.description') }}
            </p>
        </div>
    </div>

    {{-- Right Panel - Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
        <div class="w-full max-w-md">
            <div class="mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition mb-8">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back') }}
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('auth/login.form.title') }}
                </h1>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    @if (\App\Models\Setting::get('login_with_name', false))
                        <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                            {{ __('auth/login.form.username_or_email') }}
                        </label>
                        <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    @else
                        <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                            {{ __('auth/login.form.email') }}
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                               class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    @endif
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label for="password" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('auth/login.form.password') }}
                    </label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="size-4 rounded-sm border border-gray-300 bg-white text-gray-600 focus:ring-gray-600 dark:border-gray-600 dark:bg-gray-800" />
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('auth/login.form.remember_me') }}
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline transition">
                            {{ __('auth/login.form.forgot_password') }}
                        </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    {{ __('auth/login.form.login') }}
                </button>
            </form>

            @settingsRegistrationOpen
                <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                        {{ __('Register') }}
                    </a>
                </p>
            @endsettingsRegistrationOpen
        </div>
    </div>
</div>
@endsection
