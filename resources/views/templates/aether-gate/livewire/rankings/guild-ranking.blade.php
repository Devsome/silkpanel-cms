<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="ag-text-muted text-sm">{{ e($title) }}</p>
        <div class="relative w-full sm:w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 ag-text-muted pointer-events-none"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="{{ __('ranking.search_guild_placeholder') }}"
                class="ag-input w-full pl-9 pr-4 py-2 text-sm" />
        </div>
    </div>

    @if ($rows->count() > 0)
        <div class="ag-card overflow-hidden">
            <table class="ag-table">
                <thead>
                    <tr>
                        <th>#</th>
                        @foreach ($columns as $col)
                            <th>{{ e($col['label']) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php $rank = $startRank + $loop->index; @endphp
                        <tr>
                            <td class="w-12">
                                <span class="ag-font-mono text-sm font-bold
                                    {{ $rank === 1 ? 'ag-rank-1' : ($rank === 2 ? 'ag-rank-2' : ($rank === 3 ? 'ag-rank-3' : 'ag-text-muted')) }}">
                                    {{ $rank }}
                                </span>
                            </td>
                            @foreach ($columns as $col)
                                <td>
                                    @php $value = $row->{$col['column']} ?? '—'; @endphp
                                    @if ($col['column'] === 'Name')
                                        <a href="{{ route('ranking.guilds.show', Str::slug($row->Name ?? '') ?: $row->ID) }}"
                                            class="font-semibold ag-text-surface hover:ag-text-primary transition-colors">
                                            {{ e((string) $value) }}
                                        </a>
                                    @elseif ($col['column'] === 'CrestIcon' && !empty($row->CrestDataUri))
                                        <img src="{{ $row->CrestDataUri }}" alt="Crest" class="w-8 h-8">
                                    @elseif ($col['column'] === 'ItemPoints')
                                        <span class="ag-stat-amber font-semibold">{{ number_format((int) $value) }}</span>
                                    @elseif ($col['column'] === 'TotalMember')
                                        <span class="ag-stat-number">{{ $value }}</span>
                                    @elseif ($col['column'] === 'FoundationDate' && $value !== '—')
                                        <span class="ag-text-muted">{{ \Carbon\Carbon::parse($value)->format('d M Y') }}</span>
                                    @else
                                        <span class="ag-text-muted">{{ e((string) $value) }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($paginate && $rows->hasPages())
            <div class="mt-4">{{ $rows->links() }}</div>
        @endif
    @else
        <div class="text-center py-16 ag-card">
            <p class="ag-text-muted text-sm">{{ __('ranking.no_data') }}</p>
        </div>
    @endif
</div>
