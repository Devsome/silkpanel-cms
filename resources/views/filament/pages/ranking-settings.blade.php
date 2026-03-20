<x-filament-panels::page>
    {{ $this->form }}

    @if ($this->previewError)
        <div class="mt-6 rounded-xl border border-danger-200 bg-danger-50 p-4 dark:border-danger-700 dark:bg-danger-950"
            wire:transition>
            <p class="mb-1 text-sm font-semibold text-danger-700 dark:text-danger-300">
                {{ __('filament/rankings.preview.error_title') }}
            </p>
            <pre class="whitespace-pre-wrap break-all font-mono text-xs text-danger-600 dark:text-danger-400">{{ $this->previewError }}</pre>
        </div>
    @endif

    @if (!empty($this->previewData) && !empty($this->previewColumns))
        <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
            wire:transition>
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-white/10">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                        {{ __('filament/rankings.preview.title') }}
                    </h3>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        {{ count($this->previewData) }} {{ __('filament/rankings.preview.rows') }}
                        &mdash;
                        {{ __('filament/rankings.preview.live_hint') }}
                    </p>
                </div>
            </div>

            <div
                class="flex items-center gap-2 border-b border-warning-100 bg-warning-50 px-6 py-3 dark:border-warning-700/40 dark:bg-warning-950/40">
                <x-heroicon-o-information-circle class="h-4 w-4 shrink-0 text-warning-600 dark:text-warning-400" />
                <p class="text-xs text-warning-700 dark:text-warning-300">
                    {{ __('filament/rankings.preview.preview_limit_hint') }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th
                                class="w-12 px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                #
                            </th>
                            @foreach ($this->previewColumns as $col)
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ $col['label'] ?? $col['column'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($this->previewData as $rank => $row)
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 font-mono text-xs text-gray-400 dark:text-gray-500">
                                    {{ $rank + 1 }}
                                </td>
                                @foreach ($this->previewColumns as $col)
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                        @if ($this->previewTab === 'guilds' && ($col['column'] ?? null) === 'CrestIcon')
                                            @if (!empty($row['CrestIcon']))
                                                <img src="{{ $row['CrestIcon'] }}" alt="Guild Crest"
                                                    class="h-8 w-8 rounded-sm border border-gray-200 object-cover dark:border-white/10">
                                            @endif
                                        @else
                                            {{ $row[$col['column']] ?? '-' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
