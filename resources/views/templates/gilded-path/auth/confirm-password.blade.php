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
                            {{ __('auth/confirm-password.title') }}
                        </span>
                    </h2>
                    <p class="mt-4 gp-text-on-surface-variant leading-relaxed">
                        {{ __('auth/confirm-password.description') }}
                    </p>
                </div>
            </div>

            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color: var(--gp-background);">
                <div class="w-full max-w-md">
                    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h1
                                class="text-2xl font-bold font-headline gp-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/confirm-password.form.title') }}
                            </h1>
                            <p class="mt-2 text-sm gp-text-on-surface-variant">
                                {{ __('auth/confirm-password.form.description') }}
                            </p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium gp-text-on-surface-variant mb-1.5">
                                {{ __('auth/confirm-password.form.password') }}
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="gp-input border-0 block w-full px-4 py-2.5 transition" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <button type="submit"
                            class="w-full gp-gold-btn px-6 py-3 text-sm font-bold font-headline uppercase tracking-widest shadow-lg transition">
                            {{ __('auth/confirm-password.form.submit') }}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
