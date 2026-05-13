@extends('template::layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="bg-zinc-900 border border-violet-500/20 p-8 shadow-[0_0_50px_rgba(139,92,246,0.1)]">
                <div class="mb-6">
                    <p class="text-xs font-mono uppercase tracking-[0.3em] text-violet-400/70 mb-2">
                        {{ __('auth/reset-password.title') }}</p>
                    <div class="h-px bg-linear-to-r from-violet-500/50 to-transparent"></div>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('auth/reset-password.form.email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                            required autofocus
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition">
                        @error('email')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('auth/reset-password.form.password') }}</label>
                        <input id="password" type="password" name="password" required
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition"
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('auth/reset-password.form.password_confirmation') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition"
                            placeholder="••••••••">
                        @error('password_confirmation')
                            <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_20px_rgba(139,92,246,0.4)]">
                        {{ __('auth/reset-password.form.submit') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
