<?php

namespace App\Livewire;

use App\Enums\UsergroupRoleEnums;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Throwable;

class EnvEditor extends Component
{
    public bool $showPasswordModal = false;

    public bool $showEditorModal = false;

    public string $password = '';

    public ?string $passwordError = null;

    public string $content = '';

    public function mount(): void
    {
        abort_unless($this->authorized(), 403);
    }

    protected function authorized(): bool
    {
        return Auth::check() && Auth::user()->hasRole(UsergroupRoleEnums::ADMIN->value);
    }

    protected function passwordConfirmed(): bool
    {
        $confirmedAt = session('auth.password_confirmed_at');

        return $confirmedAt !== null
            && (time() - $confirmedAt) < config('auth.password_timeout', 10800);
    }

    public function openModal(): void
    {
        abort_unless($this->authorized(), 403);

        if ($this->passwordConfirmed()) {
            $this->loadEnvContent();
            $this->showEditorModal = true;

            return;
        }

        $this->password = '';
        $this->passwordError = null;
        $this->showPasswordModal = true;
    }

    public function confirmPassword(): void
    {
        abort_unless($this->authorized(), 403);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            $this->password = '';
            $this->passwordError = __('filament/env-editor.invalid_password');

            return;
        }

        session()->put('auth.password_confirmed_at', time());

        $this->password = '';
        $this->passwordError = null;
        $this->showPasswordModal = false;

        $this->loadEnvContent();
        $this->showEditorModal = true;
    }

    protected function envPath(): string
    {
        return base_path('.env');
    }

    protected function loadEnvContent(): void
    {
        $path = $this->envPath();

        $this->content = File::exists($path) ? File::get($path) : '';
    }

    public function save(): void
    {
        abort_unless($this->authorized(), 403);

        if (! $this->passwordConfirmed()) {
            $this->showEditorModal = false;
            $this->showPasswordModal = true;

            return;
        }

        $path = $this->envPath();

        if (! File::exists($path) || ! is_writable($path)) {
            Notification::make()
                ->title(__('filament/env-editor.write_error'))
                ->danger()
                ->send();

            return;
        }

        try {
            File::put($path, $this->content);
        } catch (Throwable) {
            Notification::make()
                ->title(__('filament/env-editor.write_error'))
                ->danger()
                ->send();

            return;
        }

        $this->showEditorModal = false;
        $this->content = '';

        Notification::make()
            ->title(__('filament/env-editor.saved_success'))
            ->success()
            ->send();
    }

    public function closeEditorModal(): void
    {
        $this->showEditorModal = false;
        $this->content = '';
    }

    public function closePasswordModal(): void
    {
        $this->showPasswordModal = false;
        $this->password = '';
        $this->passwordError = null;
    }

    public function render()
    {
        return view('livewire.env-editor');
    }
}
