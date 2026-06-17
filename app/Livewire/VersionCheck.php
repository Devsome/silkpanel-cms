<?php

namespace App\Livewire;

use App\Services\VersionService;
use Livewire\Component;

class VersionCheck extends Component
{
    public bool $hasSeen = true;
    public string $currentVersion = 'unknown';
    public array $entry = [];

    public function mount(): void
    {
        $service = app(VersionService::class);

        $this->currentVersion = $service->getLocalVersion();
        $this->hasSeen = $service->hasUserSeenCurrentVersion();

        if (! $this->hasSeen) {
            $changelog = $service->getLocalChangelog();
            $this->entry = $changelog[$this->currentVersion] ?? [];
        }
    }

    public function dismiss(): void
    {
        app(VersionService::class)->markCurrentVersionAsSeen();
        $this->hasSeen = true;
    }

    public function render()
    {
        return view('livewire.version-check-hook');
    }
}
