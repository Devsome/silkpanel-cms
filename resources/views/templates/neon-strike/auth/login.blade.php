@extends('template::layouts.guest')

@section('content')
    <div class="min-h-screen flex">
        {{-- Left: Decorative panel --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-zinc-950 items-center justify-center">
            <div class="absolute inset-0"
                style="background-image: linear-gradient(rgba(139,92,246,0.07) 1px, transparent 1px), linear-gradient(90deg, rgba(139,92,246,0.07) 1px, transparent 1px); background-size: 30px 30px;">
            </div>
            <div class="absolute inset-0 bg-linear-to-br from-violet-900/20 via-transparent to-fuchsia-900/15"></div>
            <div class="relative z-10 text-center p-12">
                <p class="text-xs font-mono uppercase tracking-[0.4em] text-violet-400/60 mb-4">
                    {{ __('auth/login.title', ['app_name' => '']) }}</p>
                <h2
                    class="text-4xl font-black uppercase tracking-[0.1em] bg-linear-to-r from-violet-400 via-fuchsia-400 to-cyan-400 bg-clip-text text-transparent">
                    @settings('site_title', 'SilkPanel')
                </h2>
                <p class="mt-4 text-zinc-500 text-sm max-w-xs mx-auto leading-relaxed">{{ __('auth/login.description') }}</p>
                <div class="mt-8 w-16 h-px bg-linear-to-r from-transparent via-violet-500 to-transparent mx-auto"></div>
            </div>
        </div>

        {{-- Right: Form --}}
        <div class="flex-1 flex items-center justify-center px-6 py-12 bg-black lg:bg-zinc-950">
            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                        {{ __('auth/login.form.title') }}</p>
                    <div class="h-px bg-linear-to-r from-violet-500/50 to-transparent"></div>
                </div>

                <x-auth-session-status
                    class="mb-4 p-3 border border-violet-500/30 bg-violet-500/10 text-violet-300 text-xs font-mono"
                    :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        @if (\App\Models\Setting::get('login_with_name', false))
                            <label for="email"
                                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">
                                {{ __('auth/login.form.username_or_email') }}
                            </label>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition placeholder-zinc-600"
                                placeholder="{{ __('auth/login.form.username_or_email') }}">
                        @else
                            <label for="email"
                                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">
                                {{ __('auth/login.form.email') }}
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition placeholder-zinc-600"
                                placeholder="you@example.com">
                        @endif
                        @error('email')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">
                            {{ __('auth/login.form.password') }}
                        </label>
                        <input id="password" type="password" name="password" required
                            class="w-full bg-zinc-900 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition placeholder-zinc-600"
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-3.5 h-3.5 bg-zinc-900 border border-zinc-600 text-violet-500 focus:ring-violet-500/30">
                            <span class="text-xs font-mono text-zinc-500">{{ __('auth/login.form.remember_me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-mono text-zinc-600 hover:text-violet-400 transition uppercase tracking-wider">
                                {{ __('auth/login.form.forgot_password') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:shadow-[0_0_30px_rgba(139,92,246,0.6)]">
                        {{ __('auth/login.form.submit') }}
                    </button>

                    @settingsRegistrationOpen
                        <p class="text-center text-xs font-mono text-zinc-600">
                            {{ __('auth/login.form.no_account') }}
                            <a href="{{ route('register') }}"
                                class="text-violet-400 hover:text-violet-300 transition">{{ __('auth/login.form.register') }}</a>
                        </p>
                    @endsettingsRegistrationOpen
                </form>
            </div>
        </div>
    </div>
@endsection
