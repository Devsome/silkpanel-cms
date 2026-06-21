<?php

namespace App\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class ClearCacheButton extends Component
{
    public bool $showModal = false;

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function clearCache(): void
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        $this->showModal = false;

        Notification::make()
            ->title(__('filament/cache.cleared_success'))
            ->success()
            ->send();
    }

    public function optimize(): void
    {
        Notification::make()
            ->title(__('filament/cache.optimized_success'))
            ->success()
            ->send();

        Artisan::call('optimize');

        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.clear-cache-button');
    }
}
