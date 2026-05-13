<div class="bg-zinc-900 border border-violet-500/20 p-6">
    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-2">{{ __('profile.password_section') }}
    </p>
    <h2 class="text-base font-bold uppercase tracking-widest text-white mb-5">{{ __('profile.password_title') }}</h2>

    @if (session('status') === 'password-updated')
        <div class="mb-4 p-3 border border-violet-500/30 bg-violet-500/10 text-violet-300 text-xs font-mono">
            {{ __('profile.password_updated') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password"
                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('profile.form.current_password') }}</label>
            <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition"
                placeholder="••••••••">
            @error('current_password', 'updatePassword')
                <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password"
                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('profile.form.new_password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition"
                placeholder="••••••••">
            @error('password', 'updatePassword')
                <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation"
                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('profile.form.confirm_password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password"
                class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition"
                placeholder="••••••••">
            @error('password_confirmation', 'updatePassword')
                <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="px-6 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)]">
            {{ __('profile.form.save') }}
        </button>
    </form>
</div>
