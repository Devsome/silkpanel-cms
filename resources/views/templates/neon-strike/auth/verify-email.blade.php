@extends('template::layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-zinc-900 border border-violet-500/20 p-8 text-center shadow-[0_0_50px_rgba(139,92,246,0.1)]">
                <div
                    class="w-14 h-14 border border-violet-500/40 flex items-center justify-center mx-auto mb-6 shadow-[0_0_20px_rgba(139,92,246,0.2)]">
                    <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                    {{ __('auth/verify-email.title') }}</p>
                <div class="h-px bg-linear-to-r from-transparent via-violet-500/50 to-transparent mb-4"></div>
                <p class="text-sm text-zinc-500 leading-relaxed mb-6">{{ __('auth/verify-email.description') }}</p>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-3 border border-violet-500/30 bg-violet-500/10 text-violet-300 text-xs font-mono">
                        {{ __('auth/verify-email.link_sent') }}
                    </div>
                @endif

                <div class="flex flex-col gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="w-full py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition">
                            {{ __('auth/verify-email.form.resend') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full py-2 text-xs font-mono uppercase tracking-wider text-zinc-600 hover:text-zinc-400 transition">
                            {{ __('auth/verify-email.form.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
