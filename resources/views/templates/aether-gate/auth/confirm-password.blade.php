@extends('template::layouts.app')

@section('content')
    <section class="min-h-screen">
        <div class="lg:grid lg:min-h-screen lg:grid-cols-2">
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
                            {{ __('auth/confirm-password.title') }}
                        </span>
                    </h2>
                    <p class="mt-4 ag-text-muted leading-relaxed">
                        {{ __('auth/confirm-password.description') }}
                    </p>
                </div>
            </div>

            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color:var(--ag-background);">
                <div class="w-full max-w-md">
                    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                        @csrf

                        <div>
                            <div class="mb-1 h-0.5 w-10" style="background:var(--ag-primary);"></div>
                            <h1 class="text-2xl font-bold ag-font-display ag-text-primary sm:text-3xl uppercase tracking-widest">
                                {{ __('auth/confirm-password.form.title') }}
                            </h1>
                            <p class="mt-2 text-sm ag-text-muted">
                                {{ __('auth/confirm-password.form.description') }}
                            </p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium ag-text-muted mb-1.5">
                                {{ __('auth/confirm-password.form.password') }}
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="ag-input block w-full px-4 py-2.5 transition" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <button type="submit"
                            class="ag-btn-primary w-full px-6 py-3 text-sm font-bold ag-font-display uppercase tracking-widest shadow-lg transition">
                            {{ __('auth/confirm-password.form.submit') }}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
