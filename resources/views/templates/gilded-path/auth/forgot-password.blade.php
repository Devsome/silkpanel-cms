@extends('template::layouts.app')

@section('content')
    <section class="min-h-screen">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
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
                            {{ __('auth/forgot-password.title') }}
                        </span>
                    </h2>
                    <p class="mt-4 gp-text-on-surface-variant leading-relaxed">
                        {{ __('auth/forgot-password.description') }}
                    </p>
                </div>
            </div>

            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color: var(--gp-background);">
                <div class="w-full max-w-md">
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h1
                                class="text-2xl font-bold font-headline gp-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/forgot-password.form.title') }}
                            </h1>
                            <p class="mt-2 text-sm gp-text-on-surface-variant">
                                {{ __('auth/forgot-password.form.description') }}
                            </p>
                        </div>

                        <x-auth-session-status class="bg-yellow-900/20 p-4 text-yellow-400 text-sm"
                            style="border: 1px solid rgba(242,202,80,0.2);" :status="session('status')" />

                        <div>
                            <label for="email" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                {{ __('auth/forgot-password.form.email') }}
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus class="gp-input block w-full px-4 py-2.5 transition" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <button type="submit"
                            class="w-full gp-gold-btn px-6 py-3 text-sm font-bold font-headline uppercase tracking-widest shadow-lg transition">
                            {{ __('auth/forgot-password.form.submit') }}
                        </button>

                        <a href="{{ route('login') }}"
                            class="block text-sm gp-text-on-surface-variant hover:text-yellow-400 transition">
                            {{ __('auth/forgot-password.form.already_have_account') }}
                        </a>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
