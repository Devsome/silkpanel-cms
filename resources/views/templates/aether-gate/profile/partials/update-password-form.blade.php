<section>
    <header>
        <h2 class="text-lg ag-font-display font-bold uppercase tracking-widest ag-text-primary">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm ag-text-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium ag-text-muted mb-1.5">
                {{ __('Current Password') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                autocomplete="current-password" class="ag-input block w-full px-4 py-2.5 transition" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium ag-text-muted mb-1.5">
                {{ __('New Password') }}
            </label>
            <input id="update_password_password" name="password" type="password"
                autocomplete="new-password" class="ag-input block w-full px-4 py-2.5 transition" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium ag-text-muted mb-1.5">
                {{ __('Confirm Password') }}
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password" class="ag-input block w-full px-4 py-2.5 transition" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="ag-btn-primary px-4 py-2 text-xs ag-font-display font-bold uppercase tracking-widest">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm ag-text-muted">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
