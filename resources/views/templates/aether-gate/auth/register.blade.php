@extends('template::layouts.app')

@section('content')
    <section class="min-h-screen">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
            {{-- Left: Image / Branding --}}
            <div class="relative hidden lg:flex items-end" style="background-color:var(--ag-background);">
                @if (\App\Helpers\SettingHelper::get('background_image'))
                    <img alt="" src="{{ asset('storage/' . \App\Helpers\SettingHelper::get('background_image')) }}"
                        class="absolute inset-0 h-full w-full object-cover opacity-50" />
                @else
                    <img alt="" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                        class="absolute inset-0 h-full w-full object-cover opacity-50" />
                @endif
                <div class="absolute inset-0"
                    style="background:linear-gradient(to right,rgba(6,8,15,0.3),rgba(6,8,15,1));"></div>
                <div class="absolute inset-0 pointer-events-none"
                    style="background:radial-gradient(ellipse at 20% 80%,rgba(34,211,238,0.08) 0%,transparent 60%);"></div>
                <div class="relative p-12">
                    <div class="mb-4 h-0.5 w-16" style="background:var(--ag-primary);"></div>
                    <h2 class="text-3xl font-bold ag-font-display md:text-4xl">
                        <span class="ag-text-primary drop-shadow-lg uppercase tracking-widest">
                            {{ __('auth/register.title', ['app_name' => config('app.name')]) }}
                        </span>
                    </h2>
                    <p class="mt-4 ag-text-muted leading-relaxed">
                        {{ __('auth/register.description') }}
                    </p>
                </div>
            </div>

            {{-- Right: Register Form --}}
            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color:var(--ag-background);">
                <div class="w-full max-w-lg">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <div>
                            <div class="mb-1 h-0.5 w-10" style="background:var(--ag-primary);"></div>
                            <h1 class="text-2xl font-bold ag-font-display ag-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/register.form.title') }}
                            </h1>
                            <p class="mt-2 text-sm ag-text-muted">
                                {{ __('auth/register.form.description') }}
                            </p>
                        </div>

                        <x-auth-session-status
                            class="p-4 text-sm"
                            style="background:rgba(34,211,238,0.08);border:1px solid rgba(34,211,238,0.2);color:var(--ag-primary);"
                            :status="session('status')" />

                        <x-validation-errors
                            class="p-4 text-sm text-red-400"
                            style="background:rgba(255,100,100,0.05);border:1px solid rgba(255,100,100,0.2);" />

                        {{-- Silkroad ID & Name --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="silkroad_id" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/register.form.silkroad_id') }}
                                </label>
                                <input id="silkroad_id" type="text" name="silkroad_id" value="{{ old('silkroad_id') }}"
                                    required autofocus class="ag-input block w-full px-4 py-2.5 transition" />
                            </div>
                            <div>
                                <label for="name" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/register.form.name') }}
                                </label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    autocomplete="name" class="ag-input block w-full px-4 py-2.5 transition" />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium ag-text-muted mb-1.5">
                                {{ __('auth/register.form.email') }}
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" class="ag-input block w-full px-4 py-2.5 transition" />
                        </div>

                        {{-- Passwords --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/register.form.password') }}
                                </label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="ag-input block w-full px-4 py-2.5 transition" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/register.form.confirm_password') }}
                                </label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password" class="ag-input block w-full px-4 py-2.5 transition" />
                            </div>
                        </div>

                        {{-- Terms --}}
                        @if ($tosEnabled)
                            <div>
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="terms" id="terms" value="1"
                                        {{ old('terms') ? 'checked' : '' }}
                                        class="mt-0.5 h-4 w-4"
                                        style="accent-color:var(--ag-primary);">
                                    <span class="text-sm ag-text-muted">
                                        {{ __('auth/register.form.terms_accept') }}
                                        <a href="{{ route('terms') }}" target="_blank"
                                            class="ag-text-primary underline hover:opacity-80">
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
                                <label for="referral" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/register.form.referral_code') }}
                                </label>
                                <input id="referral" type="text" name="referral"
                                    value="{{ old('referral', request('ref')) }}" autocomplete="off"
                                    class="ag-input block w-full px-4 py-2.5 transition" />
                                @error('referral')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Submit --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <button type="submit"
                                class="ag-btn-primary px-6 py-3 text-sm font-bold ag-font-display uppercase tracking-widest shadow-lg transition">
                                {{ __('auth/register.form.register') }}
                            </button>
                            <a href="{{ route('login') }}"
                                class="text-sm ag-text-muted hover:ag-text-primary transition">
                                {{ __('auth/register.form.already_registered') }}
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
