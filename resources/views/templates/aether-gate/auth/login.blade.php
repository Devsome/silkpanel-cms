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
                {{-- Cyan glow accent --}}
                <div class="absolute inset-0 pointer-events-none"
                    style="background:radial-gradient(ellipse at 20% 80%,rgba(34,211,238,0.08) 0%,transparent 60%);"></div>
                <div class="relative p-12">
                    <div class="mb-4 h-0.5 w-16" style="background:var(--ag-primary);"></div>
                    <h2 class="text-3xl font-bold ag-font-display md:text-4xl">
                        <span class="ag-text-primary drop-shadow-lg uppercase tracking-widest">
                            {{ __('auth/login.title', ['app_name' => config('app.name')]) }}
                        </span>
                    </h2>
                    <p class="mt-4 ag-text-muted leading-relaxed">
                        {{ __('auth/login.description') }}
                    </p>
                </div>
            </div>

            {{-- Right: Login Form --}}
            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color:var(--ag-background);">
                <div class="w-full max-w-md">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div>
                            <div class="mb-1 h-0.5 w-10" style="background:var(--ag-primary);"></div>
                            <h1 class="text-2xl font-bold ag-font-display ag-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/login.form.title') }}
                            </h1>
                        </div>

                        <x-auth-session-status
                            class="p-4 text-sm"
                            style="background:rgba(34,211,238,0.08);border:1px solid rgba(34,211,238,0.2);color:var(--ag-primary);"
                            :status="session('status')" />

                        {{-- Email / Username --}}
                        <div>
                            @if (\App\Models\Setting::get('login_with_name', false))
                                <label for="email" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/login.form.username_or_email') }}
                                </label>
                                <input id="email" type="text" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username" class="ag-input block w-full px-4 py-2.5 transition" />
                            @else
                                <label for="email" class="block text-sm font-medium ag-text-muted mb-1.5">
                                    {{ __('auth/login.form.email') }}
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email" class="ag-input block w-full px-4 py-2.5 transition" />
                            @endif
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium ag-text-muted mb-1.5">
                                {{ __('auth/login.form.password') }}
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="ag-input block w-full px-4 py-2.5 transition" />
                            @error('password')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="h-4 w-4 rounded"
                                    style="background:rgba(13,18,36,0.8);border-color:rgba(34,211,238,0.3);accent-color:var(--ag-primary);">
                                <span class="text-sm ag-text-muted">{{ __('auth/login.form.remember_me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm ag-text-primary hover:opacity-80 transition">
                                    {{ __('auth/login.form.forgot_password') }}
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="ag-btn-primary w-full px-6 py-3 text-sm font-bold ag-font-display uppercase tracking-widest shadow-lg transition">
                            {{ __('auth/login.form.login') }}
                        </button>

                        @if (Route::has('register'))
                            <p class="text-center text-sm ag-text-muted">
                                {{ __('auth/login.form.no_account', ['default' => "Don't have an account?"]) }}
                                <a href="{{ route('register') }}" class="ag-text-primary hover:opacity-80 transition ml-1">
                                    {{ __('auth/register.form.register') }}
                                </a>
                            </p>
                        @endif
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
