@extends('template::layouts.app')

@section('content')
    <section class="min-h-screen">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
            {{-- Left: Image / Branding --}}
            <div class="relative hidden lg:flex items-end" style="background-color: var(--gp-background);">
                @if (\App\Helpers\SettingHelper::get('background_image'))
                    <img alt="" src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}"
                        class="absolute inset-0 h-full w-full object-cover opacity-60" />
                @else
                    <img alt="" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                        class="absolute inset-0 h-full w-full object-cover opacity-60" />
                @endif
                <div class="absolute inset-0"
                    style="background: linear-gradient(to right, rgba(19,19,19,0.3), rgba(19,19,19,1));"></div>
                <div class="relative p-12">
                    <h2 class="text-3xl font-bold font-headline md:text-4xl">
                        <span class="gp-text-primary drop-shadow-lg uppercase tracking-widest">
                            {{ __('auth/register.title', ['app_name' => config('app.name')]) }}
                        </span>
                    </h2>
                    <p class="mt-4 gp-text-on-surface-variant leading-relaxed">
                        {{ __('auth/register.description') }}
                    </p>
                </div>
            </div>

            {{-- Right: Register Form --}}
            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color: var(--gp-background);">
                <div class="w-full max-w-lg">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h1
                                class="text-2xl font-bold font-headline gp-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/register.form.title') }}
                            </h1>
                            <p class="mt-2 text-sm gp-text-on-surface-variant">
                                {{ __('auth/register.form.description') }}
                            </p>
                        </div>

                        <x-auth-session-status class="bg-yellow-900/20 p-4 text-yellow-400 text-sm"
                            style="border: 1px solid rgba(242,202,80,0.2);" :status="session('status')" />

                        <x-validation-errors class="bg-red-900/20 p-4 text-red-400 text-sm"
                            style="border: 1px solid rgba(255,100,100,0.2);" />

                        {{-- Silkroad ID & Name --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="silkroad_id"
                                    class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/register.form.silkroad_id') }}
                                </label>
                                <input id="silkroad_id" type="text" name="silkroad_id" value="{{ old('silkroad_id') }}"
                                    required autofocus class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                            </div>
                            <div>
                                <label for="name" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/register.form.name') }}
                                </label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    autocomplete="name" class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                {{ __('auth/register.form.email') }}
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                        </div>

                        {{-- Passwords --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/register.form.password') }}
                                </label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/register.form.confirm_password') }}
                                </label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password"
                                    class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                            </div>
                        </div>

                        {{-- Terms --}}
                        @if ($tosEnabled)
                            <div>
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="terms" id="terms" value="1"
                                        {{ old('terms') ? 'checked' : '' }}
                                        class="mt-0.5 h-4 w-4 border-yellow-900 bg-neutral-900 text-yellow-500 focus:ring-yellow-500/20">
                                    <span class="text-sm gp-text-on-surface-variant">
                                        {{ __('auth/register.form.terms_accept') }}
                                        <a href="{{ route('terms') }}" target="_blank"
                                            class="gp-text-primary underline hover:text-yellow-300">
                                            {{ __('auth/register.form.terms_link') }}
                                        </a>
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($referralEnabled)
                            <div>
                                <label for="referral" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/register.form.referral_code') }}
                                </label>
                                <input id="referral" type="text" name="referral"
                                    value="{{ old('referral', request('ref')) }}" autocomplete="off"
                                    class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                                @error('referral')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Submit --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <button type="submit"
                                class="gp-gold-btn px-6 py-3 text-sm font-bold font-headline uppercase tracking-widest shadow-lg transition">
                                {{ __('auth/register.form.register') }}
                            </button>
                            <a href="{{ route('login') }}"
                                class="text-sm gp-text-on-surface-variant hover:text-yellow-400 transition">
                                {{ __('auth/register.form.already_registered') }}
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
