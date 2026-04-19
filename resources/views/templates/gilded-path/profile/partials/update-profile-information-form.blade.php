<section>
    <header>
        <h2 class="text-lg font-headline font-bold uppercase tracking-widest gp-text-primary">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm gp-text-on-surface-variant">
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
            <x-input-label for="name" :value="__('Name')" class="gp-text-on-surface" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full gp-input"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="gp-text-on-surface" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full gp-input"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 gp-text-on-surface-variant">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm gp-text-primary hover:text-yellow-300 rounded-md focus:outline-none">
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
            <button type="submit"
                class="px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-gold-btn">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm gp-text-on-surface-variant">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
