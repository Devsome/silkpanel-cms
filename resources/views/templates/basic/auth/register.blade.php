@extends('template::layouts.guest', ['title' => __('auth/register.form.title')])

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900 px-4 py-12">
        @if (\App\Helpers\SettingHelper::get('background_image'))
            <img src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}" alt=""
                class="absolute inset-0 h-full w-full object-cover opacity-15" />
        @endif

        <div class="relative w-full max-w-lg">
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
                    {{ __('auth/register.form.title') }}
                </h1>
                <p class="mt-2 text-gray-400">
                    {{ __('auth/register.form.description') }}
                </p>
            </div>

            {{-- Register Card --}}
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8 shadow-2xl">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="silkroad_id" class="block text-sm font-medium text-gray-200">
                            {{ __('auth/register.form.silkroad_id') }}
                        </label>
                        <input id="silkroad_id" name="silkroad_id" type="text" value="{{ old('silkroad_id') }}" required
                            autofocus
                            class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        <x-input-error :messages="$errors->get('silkroad_id')" class="mt-1" />
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-200">
                            {{ __('auth/register.form.name') }}
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-200">
                            {{ __('auth/register.form.email') }}
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-200">
                                {{ __('auth/register.form.password') }}
                            </label>
                            <input id="password" name="password" type="password" required
                                class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-200">
                                {{ __('auth/register.form.password_confirmation') }}
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="mt-2 block w-full rounded-xl bg-white/10 border border-white/20 px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" />
                        </div>
                    </div>

                    @if (\App\Helpers\SettingHelper::get('tos_enabled'))
                        <div>
                            <label for="terms" class="inline-flex items-start">
                                <input id="terms" type="checkbox" name="terms" required
                                    class="mt-1 size-4 rounded border-white/30 bg-white/10 text-indigo-500 focus:ring-indigo-500" />
                                <span class="ml-2 text-sm text-gray-300">
                                    {!! __('I agree to the :terms', [
                                        'terms' =>
                                            '<a href="' .
                                            url('/terms') .
                                            '" target="_blank" class="text-indigo-400 hover:text-indigo-300 underline">' .
                                            __('auth/register.form.terms_link') .
                                            '</a>',
                                    ]) !!}
                                </span>
                            </label>
                            <x-input-error :messages="$errors->get('terms')" class="mt-1" />
                        </div>
                    @endif

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-lg shadow-indigo-500/25">
                        {{ __('auth/register.form.register') }}
                    </button>
                </form>
            </div>

            <p class="mt-8 text-center text-sm text-gray-400">
                {{ __('auth/register.form.already_registered') }}
                <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition">
                    {{ __('navigation.login') }}
                </a>
            </p>
        </div>
    </div>
@endsection
