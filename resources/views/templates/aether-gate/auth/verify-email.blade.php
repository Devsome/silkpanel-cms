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
                            {{ __('auth/verify-email.title') }}
                        </span>
                    </h2>
                    <p class="mt-4 ag-text-muted leading-relaxed">
                        {{ __('auth/verify-email.description') }}
                    </p>
                </div>
            </div>

            <main class="flex items-center justify-center px-6 py-12 lg:px-16"
                style="background-color:var(--ag-background);">
                <div class="w-full max-w-md space-y-6">
                    <div>
                        <div class="mb-1 h-0.5 w-10" style="background:var(--ag-primary);"></div>
                        <h1 class="text-2xl font-bold ag-font-display ag-text-primary sm:text-3xl uppercase tracking-widest">
                            {{ __('auth/verify-email.form.title') }}
                        </h1>
                        <p class="mt-2 text-sm ag-text-muted">
                            {{ __('auth/verify-email.form.description') }}
                        </p>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="p-4 text-sm"
                            style="background:rgba(34,211,238,0.08);border:1px solid rgba(34,211,238,0.2);color:var(--ag-primary);">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="ag-btn-primary w-full px-6 py-3 text-sm font-bold ag-font-display uppercase tracking-widest shadow-lg transition">
                            {{ __('auth/verify-email.form.resend') }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="ag-btn-secondary w-full px-6 py-3 text-sm font-bold ag-font-display uppercase tracking-widest transition">
                            {{ __('auth/verify-email.form.logout') }}
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </section>
@endsection
