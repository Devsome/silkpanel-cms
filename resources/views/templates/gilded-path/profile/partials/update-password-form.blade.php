<section>
    <header>
        <h2 class="text-lg font-headline font-bold uppercase tracking-widest gp-text-primary">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm gp-text-on-surface-variant">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="gp-text-on-surface" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full gp-input" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="gp-text-on-surface" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full gp-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="gp-text-on-surface" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full gp-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-4 py-2 text-xs font-headline font-bold uppercase tracking-widest gp-gold-btn">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm gp-text-on-surface-variant">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
