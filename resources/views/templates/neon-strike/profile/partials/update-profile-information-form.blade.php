<div class="bg-zinc-900 border border-violet-500/20 p-6">
    <p class="text-xs font-mono uppercase tracking-[0.25em] text-violet-400/70 mb-2">
        {{ __('auth/register.info_section') }}</p>
    <h2 class="text-base font-bold uppercase tracking-widest text-white mb-5">{{ __('auth/register.info_title') }}</h2>

    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-3 border border-violet-500/30 bg-violet-500/10 text-violet-300 text-xs font-mono">
            {{ __('auth/register.updated_successfully') }}
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label for="name"
                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('auth/register.form.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition">
            @error('name')
                <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email"
                class="block text-xs font-mono uppercase tracking-wider text-zinc-500 mb-1.5">{{ __('auth/register.form.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                class="w-full bg-zinc-950 border border-zinc-700 text-zinc-100 px-3 py-2.5 focus:outline-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500/30 font-mono text-sm transition">
            @error('email')
                <p class="mt-1 text-xs font-mono text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail())
            <div class="p-3 border border-amber-500/30 bg-amber-500/10 text-amber-300 text-xs font-mono">
                {{ __('auth/register.unverified_email') }}
                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="underline hover:text-amber-200 transition">{{ __('auth/register.resend_verification') }}</button>
                </form>
            </div>
        @endif

        <button type="submit"
            class="px-6 py-2 text-xs font-bold uppercase tracking-[0.2em] text-white bg-linear-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 transition shadow-[0_0_15px_rgba(139,92,246,0.3)]">
            {{ __('auth/register.form.save') }}
        </button>
    </form>
</div>
