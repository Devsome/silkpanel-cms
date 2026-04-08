@extends('template::layouts.app')

@section('content')
    <section class="min-h-screen">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
            {{-- Left: Image / Branding --}}
            <div class="relative hidden lg:flex items-end bg-gray-950">
                <img alt="" src="{{ Vite::asset('resources/images/banner/background-one.png') }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-60" />
                <div class="absolute inset-0 bg-gradient-to-r from-gray-950/30 to-gray-950"></div>
                <div class="relative p-12">
                    <h2 class="text-3xl font-black md:text-4xl">
                        <span class="bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                            {{ __('auth/register.title', ['app_name' => config('app.name')]) }}
                        </span>
                    </h2>
                    <p class="mt-4 text-gray-400 leading-relaxed">
                        {{ __('auth/register.description') }}
                    </p>
                </div>
            </div>

            {{-- Right: Register Form --}}
            <main class="flex items-center justify-center px-6 py-12 lg:px-16 bg-gray-950">
                <div class="w-full max-w-lg">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h1 class="text-2xl font-bold text-white sm:text-3xl">
                                {{ __('auth/register.form.title') }}
                            </h1>
                            <p class="mt-2 text-sm text-gray-400">
                                {{ __('auth/register.form.description') }}
                            </p>
                        </div>

                        <x-auth-session-status
                            class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-emerald-400 text-sm"
                            :status="session('status')" />

                        <x-validation-errors
                            class="rounded-xl bg-red-500/10 border border-red-500/20 p-4 text-red-400 text-sm" />

                        {{-- Silkroad ID & Name --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="silkroad_id" class="block text-sm font-medium text-gray-400 mb-1.5">
                                    {{ __('auth/register.form.silkroad_id') }}
                                </label>
                                <input id="silkroad_id" type="text" name="silkroad_id" value="{{ old('silkroad_id') }}"
                                    required autofocus
                                    class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            </div>
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-400 mb-1.5">
                                    {{ __('auth/register.form.name') }}
                                </label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    autocomplete="name"
                                    class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-400 mb-1.5">
                                {{ __('auth/register.form.email') }}
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email"
                                class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                        </div>

                        {{-- Passwords --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-400 mb-1.5">
                                    {{ __('auth/register.form.password') }}
                                </label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-400 mb-1.5">
                                    {{ __('auth/register.form.confirm_password') }}
                                </label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password"
                                    class="block w-full rounded-xl border border-gray-800 bg-gray-900 px-4 py-2.5 text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-emerald-500/20 transition" />
                            </div>
                        </div>

                        {{-- Terms --}}
                        @if ($tosEnabled)
                            <div>
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" name="terms" id="terms" value="1"
                                        {{ old('terms') ? 'checked' : '' }}
                                        class="mt-0.5 h-4 w-4 rounded border-gray-700 bg-gray-900 text-emerald-500 focus:ring-emerald-500/20">
                                    <span class="text-sm text-gray-400">
                                        {{ __('auth/register.form.terms_accept') }}
                                        <a href="{{ route('terms') }}" target="_blank"
                                            class="text-emerald-400 underline hover:text-emerald-300">
                                            {{ __('auth/register.form.terms_link') }}
                                        </a>
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Submit --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <button type="submit"
                                class="rounded-xl px-6 py-3 text-sm font-semibold text-gray-950 bg-gradient-to-r from-emerald-400 to-cyan-400 hover:from-emerald-300 hover:to-cyan-300 shadow-lg shadow-emerald-500/25 transition">
                                {{ __('auth/register.form.register') }}
                            </button>
                            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-emerald-400 transition">
                                {{ __('auth/register.form.already_registered') }}
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
