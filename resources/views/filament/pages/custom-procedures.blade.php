<x-filament::page>
    <x-locked-overlay
        :locked="$this->isLocked()"
        :title="$this->getLockedTitle()"
        :description="$this->getLockedDescription()"
        :icon="$this->getLockedIcon()"
    >
        <div class="space-y-6">
            {{ $this->form }}

            @unless ($this->isLocked())
                <div class="flex flex-wrap gap-3">
                    <x-filament::button wire:click="saveMapping">
                        Save Mapping
                    </x-filament::button>

                    <x-filament::button color="gray" wire:click="testProcedure">
                        Run Test
                    </x-filament::button>
                </div>
            @endunless

            @if ($testResult)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Last Test Result</h3>
                    <pre class="mt-3 overflow-x-auto rounded-lg bg-gray-100 p-3 text-xs text-gray-900 dark:bg-gray-950 dark:text-gray-100">{{ json_encode($testResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            @endif

            <div>
                {{ $this->table }}
            </div>
        </div>
    </x-locked-overlay>
</x-filament::page>
