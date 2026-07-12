<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ __('navigation.history') }}
                </h1>
            </div>

            <livewire:histories.global-history />
        </div>
    </div>
</x-app-layout>
