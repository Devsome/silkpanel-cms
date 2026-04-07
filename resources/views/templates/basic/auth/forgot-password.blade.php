@extends('template::layouts.guest', ['title' => __('Forgot Password')])

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 p-8">
            <div class="mb-6">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back to login') }}
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Forgot Password') }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Enter your email address and we\'ll send you a link to reset your password.') }}
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('Email') }}
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    {{ __('Send Reset Link') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
