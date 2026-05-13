@extends('template::layouts.app')

@section('content')
    <section class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="mb-6">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center gap-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-violet-400 transition">
                    ← {{ __('dashboard.back_to_dashboard') }}
                </a>
            </div>

            <div>
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-1">
                    {{ __('auth/register.section_label') }}</p>
                <h1 class="text-2xl font-black uppercase tracking-widest text-white">
                    {{ __('auth/register.title', ['app_name' => config('app.name')]) }}</h1>
                <div class="mt-3 h-px bg-linear-to-r from-violet-500/40 to-transparent"></div>
            </div>

            @include('template::profile.partials.update-profile-information-form')
            @include('template::profile.partials.update-password-form')

            {{-- Delete account --}}
            @if (config('auth.allow_account_deletion', false))
                <div class="bg-zinc-900 border border-red-500/20 p-6">
                    <p class="text-xs font-mono uppercase tracking-[0.25em] text-red-400/70 mb-2">
                        {{ __('auth/register.danger_zone') }}</p>
                    <h2 class="text-base font-bold uppercase tracking-widest text-white mb-4">
                        {{ __('auth/register.delete_account') }}</h2>
                    <p class="text-sm text-zinc-500 mb-4">{{ __('auth/register.delete_account_description') }}</p>
                    <form method="POST" action="{{ route('profile.destroy') }}"
                        onsubmit="return confirm('{{ __('auth/register.delete_account_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <input type="password" name="password" required
                            placeholder="{{ __('auth/register.confirm_password_label') }}"
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/30 font-mono text-sm transition placeholder-zinc-600 mb-3">
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-red-700 hover:bg-red-600 transition">
                            {{ __('auth/register.delete_account') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection
