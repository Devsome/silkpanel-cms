@extends('template::layouts.app')

@section('content')
    <section class="min-h-screen">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
            {{-- Left: Image / Branding --}}
            <div class="relative hidden lg:flex items-end bg-gray-950">
                <img alt="" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-60" />
                <div class="absolute inset-0 bg-linear-to-r from-gray-950/30 to-gray-950"></div>
                <div class="relative p-12">
                    <h2 class="text-3xl font-black md:text-4xl">
                        <span class="bg-linear-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                            {{ __('auth/login.title', ['app_name' => config('app.name')]) }}
                        </span>
                    </h2>
                    <p class="mt-4 text-gray-400 leading-relaxed">
                        {{ __('auth/login.description') }}
                    </p>
                </div>
            </div>

            {{-- Right: Login Form --}}
            <main class="flex items-center justify-center px-6 py-12 lg:px-16 bg-gray-950">
                <div class="w-full max-w-md">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h1 class="text-2xl font-bold text-white sm:text-3xl">
                                {{ __('auth/login.form.title') }}
                            </h1>
                        </div>

                        <x-auth-session-status
                            class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-emerald-400 text-sm"
                            :status="session('status')" />

                        {{-- Email / Username --}}
                        <div>
                            @if (\App\Models\Setting::get('login_with_name', false))
                                <label for="email" class="block text-sm font-medium text-gray-400 mb-1.5">
                                    {{ __('auth/login.form.username_or_email') }}
                                </label>
                                <input id="email" type="text" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username"
                                    class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            @else
                                <label for="email" class="block text-sm font-medium text-gray-400 mb-1.5">
                                    {{ __('auth/login.form.email') }}
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email"
                                    class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            @endif
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-400 mb-1.5">
                                {{ __('auth/login.form.password') }}
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            @error('password')
                                <p class="mt-1.5 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="h-4 w-4 rounded border-gray-700 bg-gray-900 text-emerald-500 focus:ring-emerald-500/20">
                                <span class="text-sm text-gray-400">{{ __('auth/login.form.remember_me') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-sm text-emerald-400 hover:text-emerald-300 transition">
                                    {{ __('auth/login.form.forgot_password') }}
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                            class="w-full rounded-xl px-6 py-3 text-sm font-semibold text-gray-950 bg-linear-to-r from-emerald-400 to-cyan-400 hover:from-emerald-300 hover:to-cyan-300 shadow-lg shadow-emerald-500/25 transition">
                            {{ __('auth/login.form.login') }}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
