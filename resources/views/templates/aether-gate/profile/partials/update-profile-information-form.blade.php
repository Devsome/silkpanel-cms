<section>
    <header>
        <h2 class="text-lg ag-font-display font-bold uppercase tracking-widest ag-text-primary">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm ag-text-muted">
            {{ __('Update your account\'s profile information and email address.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium ag-text-muted mb-1.5">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                autofocus autocomplete="name" class="ag-input block w-full px-4 py-2.5 transition" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium ag-text-muted mb-1.5">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                autocomplete="username" class="ag-input block w-full px-4 py-2.5 transition" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 ag-text-muted">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm ag-text-primary hover:opacity-80 rounded-md focus:outline-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="ag-btn-primary px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm ag-text-muted">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
