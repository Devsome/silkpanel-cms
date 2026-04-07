@extends('template::layouts.guest', ['title' => __('Reset Password')])

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('Reset Password') }}
                </h1>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('Email') }}
                    </label>
                    <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus readonly
                           class="mt-2 block w-full rounded-md bg-gray-50 px-3 py-1.5 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 dark:bg-gray-900 dark:text-gray-400 dark:outline-gray-600 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label for="password" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('New Password') }}
                    </label>
                    <input id="password" name="password" type="password" required
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">
                        {{ __('Confirm Password') }}
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-gray-600 dark:bg-gray-800 dark:text-white dark:outline-gray-600 dark:focus:outline-gray-400 sm:text-sm/6" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    {{ __('Reset Password') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
