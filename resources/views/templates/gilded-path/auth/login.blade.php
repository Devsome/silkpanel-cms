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
                            {{ __('auth/login.title', ['app_name' => config('app.name')]) }}
                        </span>
                    </h2>
                    <p class="mt-4 gp-text-on-surface-variant leading-relaxed">
                        {{ __('auth/login.description') }}
                    </p>
                </div>
            </div>

            {{-- Right: Login Form --}}
            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color: var(--gp-background);">
                <div class="w-full max-w-md">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h1
                                class="text-2xl font-bold font-headline gp-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/login.form.title') }}
                            </h1>
                        </div>

                        <x-auth-session-status class="bg-yellow-900/20 p-4 text-yellow-400 text-sm"
                            style="border: 1px solid rgba(242,202,80,0.2);" :status="session('status')" />

                        {{-- Email / Username --}}
                        <div>
                            @if (\App\Models\Setting::get('login_with_name', false))
                                <label for="email" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/login.form.username_or_email') }}
                                </label>
                                <input id="email" type="text" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username"
                                    class="gp-input block w-full px-4 py-2.5 transition" />
                            @else
                                <label for="email" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                    {{ __('auth/login.form.email') }}
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email" class="gp-input block w-full px-4 py-2.5 transition" />
                            @endif
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                {{ __('auth/login.form.password') }}
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="gp-input block w-full px-4 py-2.5 transition" />
                            @error('password')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="h-4 w-4 border-yellow-900 bg-neutral-900 text-yellow-500 focus:ring-yellow-500/20">
                                <span
                                    class="text-sm gp-text-on-surface-variant">{{ __('auth/login.form.remember_me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm gp-text-primary hover:text-yellow-300 transition">
                                    {{ __('auth/login.form.forgot_password') }}
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full gp-gold-btn px-6 py-3 text-sm font-bold font-headline uppercase tracking-widest shadow-lg transition">
                            {{ __('auth/login.form.login') }}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
