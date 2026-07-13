<x-filament::page>
    <x-locked-overlay
        :locked="$this->isLocked()"
        :title="$this->getLockedTitle()"
        :description="$this->getLockedDescription()"
        :icon="$this->getLockedIcon()"
    >
        {{ $this->form }}
    </x-locked-overlay>
</x-filament::page>
