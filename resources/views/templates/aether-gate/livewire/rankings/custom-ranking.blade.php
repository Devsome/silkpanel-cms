<div>
    @if (!$configured)
        <div class="text-center py-16 ag-card">
            <p class="ag-text-muted text-sm">{{ __('ranking.custom_not_configured') }}</p>
        </div>
    @else
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
                                    <td>{{ e((string) ($row->{$col['column']} ?? '—')) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($paginate && $rows->hasPages())
                <div class="mt-4">
                    {{ $rows->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16 ag-card">
                <p class="ag-text-muted text-sm">{{ __('ranking.no_data') }}</p>
            </div>
        @endif
    @endif
</div>
