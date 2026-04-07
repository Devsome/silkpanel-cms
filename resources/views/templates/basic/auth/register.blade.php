@extends('template::layouts.guest', ['title' => __('Register')])

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
                {{ __('Join our community') }}
            </h2>
            <p class="mt-4 text-lg text-gray-300">
                {{ __('Create your account and start your adventure.') }}
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
                    {{ __('Create Account') }}
                </h1>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="silkroad_id" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('auth/register.form.silkroad_id') }}
                    </label>
                    <input id="silkroad_id" name="silkroad_id" type="text" value="{{ old('silkroad_id') }}" required autofocus
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('silkroad_id')" class="mt-1" />
                </div>

                <div>
                    <label for="name" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('auth/register.form.name') }}
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('auth/register.form.email') }}
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                            {{ __('auth/register.form.password') }}
                        </label>
                        <input id="password" name="password" type="password" required
                               class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                            {{ __('auth/register.form.password_confirmation') }}
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    </div>
                </div>

                @if (\App\Helpers\SettingHelper::get('tos_enabled'))
                    <div>
                        <label for="terms" class="inline-flex items-start">
                            <input id="terms" type="checkbox" name="terms" required
                                   class="mt-1 size-4 rounded-sm border border-gray-300 bg-white text-gray-600 focus:ring-gray-600 dark:border-gray-600 dark:bg-gray-800" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                {!! __('I agree to the :terms', ['terms' => '<a href="' . url('/terms') . '" target="_blank" class="underline hover:text-gray-900 dark:hover:text-white">' . __('Terms of Service') . '</a>']) !!}
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('terms')" class="mt-1" />
                    </div>
                @endif

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    {{ __('Register') }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                    {{ __('Log in') }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
