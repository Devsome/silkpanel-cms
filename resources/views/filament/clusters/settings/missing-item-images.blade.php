<x-filament::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('filament/settings.form.missing_item_images.upload_heading') }}
            </x-slot>
            <x-slot name="description">
                {{ __('filament/settings.form.missing_item_images.upload_description') }}
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        {{ $this->table }}
    </div>
</x-filament::page>
