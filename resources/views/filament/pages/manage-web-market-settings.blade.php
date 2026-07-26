<x-filament-panels::page>
    <x-locked-overlay
        :locked="$this->isLocked()"
        :title="$this->getLockedTitle()"
        :description="$this->getLockedDescription()"
        :icon="$this->getLockedIcon()"
    >
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>
    </x-locked-overlay>
</x-filament-panels::page>
